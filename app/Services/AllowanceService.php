<?php
namespace App\Services;

use App\Models\Member;
use App\Models\Allowance;
use App\Helpers\AppHelper;
use Illuminate\Support\Str;
use App\Models\WelfareScheme;
use App\Events\AllowanceEvent;
use Illuminate\Support\Carbon;
use App\Events\BusinessActionEvent;
use App\Events\ApplicationCreateEvent;
use App\Exceptions\DuplicateApplicationNumberException;
use App\Imports\AllowancePaymentImport;
use Ynotz\MediaManager\Models\MediaItem;
use App\Models\EducationSchemeApplication;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AllowanceService
{
    public function storeEducationSchemeApplication($data)
    {
        $appln = EducationSchemeApplication::whereJsonContains('passed_exam_details->exam_reg_no', $data['passed_exam_details']['exam_reg_no'])
        ->whereJsonContains('passed_exam_details->exam_name', $data['passed_exam_details']['exam_name'])
        ->whereJsonContains('passed_exam_details->exam_start_date', $data['passed_exam_details']['exam_start_date'])
        ->get()->first();
        if ($appln != null) {
            return false;
        }
        $member = Member::find($data['member_id']);
        $applnData = collect($this->prepareForStoreValidation($data))->only([
            'member_id',
            'member_name',
            'member_address',
            'student_name',
            'fee_period_from',
            'fee_period_to',
            // 'application_date',
            'passed_exam_details',
            'arrear_months_exdt',
            'is_sc_st',
            'marks_scored',
            'total_marks',
            'member_phone',
            'member_aadhaar',
            'member_bank_account',
        ])->toArray();

        $applnData['fee_period_from'] = AppHelper::formatDateForSave($applnData['fee_period_from']);
        $applnData['fee_period_to'] = AppHelper::formatDateForSave($applnData['fee_period_to']);
        DB::beginTransaction();
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


        if (isset($data['existing'])) {
            foreach ($data['existing'] as $property => $ulid) {
                $mi = MediaItem::where('ulid', $ulid)->get()->first();
                $esa->attachMedia($mi, $property);
            }
        }
        if (isset($data['application_no'])) {
            $existingAllowances = Allowance::where('application_no', $data['application_no'])->get()->first();
            if($existingAllowances != null) {
                throw new DuplicateApplicationNumberException("The application number: ".$data['application_no']." is already in use.");
            }
        }

        $applNo = $data['application_no'] != null && strlen(trim($data['application_no'])) > 0 ? $data['application_no'] : AppHelper::getWelfareApplicationNumber($member, $data['scheme_code']);
        $existingAllowances = Allowance::where('application_no', $applNo)->get();
        while(count($existingAllowances) > 0) {
            // $applNo = AppHelper::getWelfareApplicationNumber($member, $data['scheme_code']);
            // if ($lastApplication != null) {
            $lastAppArr = explode('/', $applNo);
            $aplStr = array_pop($lastAppArr);
            $aplNumeric = intval($aplStr) + 1;
            // }
            $applNo = implode('/', [...$lastAppArr, $aplNumeric]);
            $existingAllowances = Allowance::where('application_no', $applNo)->get();
        }
        try {
            $alData = [
                'member_id' => $data['member_id'],
                'district_id' => $member->district_id,
                'allowanceable_type' => EducationSchemeApplication::class,
                'allowanceable_id' => $esa->id,
                'application_no' => $applNo,
                'unique_application_no' => $applNo,
                'application_date' => AppHelper::formatDateForSave($data['application_date']),
                'welfare_scheme_id' => WelfareScheme::where('code', $data['scheme_code'])->get()->first()->id,
                'created_by' => auth()->user()->id
            ];
        } catch (\Throwable $e) {
            # code...
        }
        $allowance = Allowance::create($alData);
        DB::commit();
        AllowanceEvent::dispatch($member->district_id, AllowanceEvent::$ACTION_CREATED, $allowance);
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

    public function updateEducationSchemeApplication($id, $data)
    {
        /**
         * @var Allowance
         */
        $allowance = Allowance::find($id);

        /**
         * @var EducationSchemeApplication
         */
        $esa = $allowance->allowanceable;
        $member = Member::find($data['member_id']);
        $applnData = collect($this->prepareForStoreValidation($data))->only([
            'member_id',
            'member_name',
            'member_address',
            'student_name',
            // 'application_date',
            'passed_exam_details',
            'arrear_months_exdt',
            'is_sc_st',
            'marks_scored',
            'total_marks',
            'member_phone',
            'member_aadhaar',
            'member_bank_account',
        ])->toArray();
        DB::beginTransaction();
        $esa->update($applnData);
        $esa->save();
        $esa->refresh();
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
            'Updated',
            auth()->user()->id,
            null,
            $esa,
            'Updated EducationAllowanceApplication with id: '.$esa->id,
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
            'application_no' => $allowance->application_no,
            'application_date' => AppHelper::formatDateForSave($data['application_date']),
            'created_by' => auth()->user()->id
        ];
        $allowance->update($alData);
        // AllowanceEvent::dispatch($member->district_id, AllowanceEvent::$ACTION_UPDATED, $allowance);
        BusinessActionEvent::dispatch(
            Allowance::class,
            $allowance->id,
            'Updated',
            auth()->user()->id,
            null,
            $allowance,
            'Updated Allowance with id: '.$allowance->id,
            $member->district_id
        );
        DB::commit();
        return $allowance;
    }

    public function approve($id, $approval, $amount = null, $reason = '')
    {
        try {
            $status = match($approval) {
                'Paid' => Allowance::$STATUS_PAID,
                'Approved' => Allowance::$STATUS_APPROVED,
                'Rejected' => Allowance::$STATUS_REJECTED,
                'Pending' => Allowance::$STATUS_PENDING,
                'Old - Unknown' => Allowance::$STATUS_OLD_UNKNOWN,
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
                'payment_date' => $a->payment_date
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

    public function processPayment($file)
    {
        $import = new AllowancePaymentImport();
        Excel::import(
            $import,
            $file
        );

        $msg = "{$import->getImportedCount()} of {$import->getTotalCount()} leads imported";

        $failed = $import->getFailedList();

        return [
            'message' => $msg,
            'failed' => $failed
        ];
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
        if (isset($data['scheme'])) {
            $query->where('welfare_scheme_id', $data['scheme']);
        }
        if (isset($data['course'])) {
            $query->whereHas(
                'allowanceable',
                function (Builder $query) use ($data) {
                    $query->whereJsonContains('passed_exam_details->exam_name', $data['course']);
                }
            );
        }
        $query->orderBy('created_at', 'desc');
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
