<?php
namespace App\Services;

use App\Models\Member;
use App\Models\Allowance;
use App\Helpers\AppHelper;
use Illuminate\Support\Str;
use App\Models\WelfareScheme;
use App\Events\AllowanceEvent;
use Illuminate\Support\Carbon;
use App\Events\ApplicationCreateEvent;
use App\Events\BusinessActionEvent;
use Ynotz\MediaManager\Models\MediaItem;
use App\Models\EducationSchemeApplication;

class AllowanceService
{
    public function storeEducationSchemeApplication($data)
    {
        $member = Member::find($data['member_id']);
        $applnData = collect($this->prepareForStoreValidation($data))->only([
            'member_id',
            'member_name',
            'member_address',
            'student_name',
            'application_date',
            'passed_exam_details',
            'arrear_months_exdt',
            'is_sc_st',
            'marks_scored',
            'total_marks',
            'member_phone',
            'member_aadhaar',
            'member_bank_account',
        ])->toArray();

        /**
         * @var EducationSchemeApplication
         */
        $esa = EducationSchemeApplication::create($applnData);
        AppHelper::syncImageFromRequestData($esa, 'mark_list', $data);
        AppHelper::syncImageFromRequestData($esa, 'tc', $data);
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_front', $data);
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_back', $data);
        AppHelper::syncImageFromRequestData($esa, 'aadhaar_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'bank_passbook', $data);
        AppHelper::syncImageFromRequestData($esa, 'union_certificate', $data);
        AppHelper::syncImageFromRequestData($esa, 'ration_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'caste_certificate', $data);
        AppHelper::syncImageFromRequestData($esa, 'one_and_same_certificate', $data);

        BusinessActionEvent::dispatch(
            EducationSchemeApplication::class,
            $esa->id,
            'Created',
            auth()->user()->id,
            null,
            $esa,
            'Created EducationAllowanceApplication with id: '.$esa->id,
            $member->district_id
        );

        if (isset($data['existing'])) {
            foreach ($data['existing'] as $property => $ulid) {
                $mi = MediaItem::where('ulid', $ulid)->get()->first();
                $esa->attachMedia($mi, $property);
            }
        }

        $alData = [
            'member_id' => $data['member_id'],
            'district_id' => $member->district_id,
            'allowanceable_type' => EducationSchemeApplication::class,
            'allowanceable_id' => $esa->id,
            'application_no' => AppHelper::getWelfareApplicationNumber($member, $data['scheme_code']),
            'application_date' => Carbon::today()->format('Y-m-d'),
            'welfare_scheme_id' => WelfareScheme::where('code', $data['scheme_code'])->get()->first()->id,
            'created_by' => auth()->user()->id
        ];
        $allowance = Allowance::create($alData);
        AllowanceEvent::dispatch($member->district_id, AllowanceEvent::$ACTION_CREATED, $allowance);
        BusinessActionEvent::dispatch(
            Allowance::class,
            $allowance->id,
            'Created',
            auth()->user()->id,
            null,
            $allowance,
            'Created Allowance with id: '.$allowance->id,
            $member->district_id
        );
        return $allowance;
    }

    public function approve($id, $approval, $amount = null, $reason = '')
    {
        try {
            $status = match($approval) {
                'Paid' => Allowance::$STATUS_PAID,
                'Approved' => Allowance::$STATUS_APPROVED,
                'Rejected' => Allowance::$STATUS_REJECTED,
            };
            $a = Allowance::find($id);
            $a->status = $status;
            if ($status == Allowance::$STATUS_REJECTED) {
                $a->rejection_reason = $reason;
            }
            if ($amount != null && $status == Allowance::$STATUS_APPROVED) {
                $a->sanctioned_amount = $amount;
                $a->sanctioned_date = Carbon::today()->format('Y-m-d');
            }
            if ($status == Allowance::$STATUS_PAID) {
                $a->payment_date = Carbon::today()->format('Y-m-d');
            }
            $a->save();
            $a->refresh();
            AllowanceEvent::dispatch($a->member->district_id, AllowanceEvent::$ACTION_APPROVED, $a);
            BusinessActionEvent::dispatch(
                Allowance::class,
                $a->id,
                $approval,
                auth()->user()->id,
                null,
                $a,
                $approval.' Allowance with id: '.$a->id,
                $a->member->district_id
            );
            return [
                'success' => true,
                'sanctioned_date' => $a->sanctioned_date,
                'sanctioned_amount' => $a->sanctioned_amount,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false
            ];
        }
    }

    public function pending()
    {
        $did = auth()->user()->district_id;
        $ps = Allowance::$STATUS_PENDING;

        return Allowance::where('status', $ps)
            ->where('district_id', $did)
            ->get();
    }

    public function report($data)
    {
        $query = Allowance::query();
        $query->userDistrictConstrained();
        $datetype = $data['datetype'];
        if (isset($data['created_by'])) {
            $query->where('created_by', $data['created_by']);
        }
        if (isset($data['start'])) {
            $query->where($datetype, '>=', AppHelper::formatDateForSave(thedate: $data['start'], setTimeTo: 'start'));
        }
        if (isset($data['end'])) {
            $query->where($datetype, '<=', AppHelper::formatDateForSave(thedate: $data['end'], setTimeTo: 'end'));
        }
        if (isset($data['status'])) {
            $statkey = 'STATUS_'.Str::upper($data['status']);
            $query->where('status', Allowance::$$statkey);
        }
        if (isset($data['fullreport']) && $data['fullreport']) {
            return $query->get();
        } else {
            return $query->paginate(
                perPage: 100,
                page: $data['page']
            );
        }
    }


    public function prepareForStoreValidation(array $data): array
    {
        $data['is_sc_st'] = $data['is_sc_st'] == 'Yes';
        $data['total_marks'] = $data['marks_total'];

        return $data;
    }

}
