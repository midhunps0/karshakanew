<?php
namespace App\Services;

use App\Events\AllowanceEvent;
use App\Events\ApplicationCreateEvent;
use App\Models\Member;
use App\Models\Allowance;
use App\Helpers\AppHelper;
use App\Models\WelfareScheme;
use Illuminate\Support\Carbon;
use Ynotz\MediaManager\Models\MediaItem;
use App\Models\EducationSchemeApplication;

class AllowanceService
{
    public function storeEducationSchemeApplication($data)
    {
        $member = Member::find($data['member_id']);
        $applnData = collect($data)->only([
            'member_id',
            'member_name',
            'member_address',
            'student_name',
            'passed_exam_details',
            'arrear_months_exdt',
            'marks_scored',
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

        if (isset($data['existing'])) {
            foreach ($data['existing'] as $property => $ulid) {
                $mi = MediaItem::where('ulid', $ulid)->get()->first();
                $esa->attachMedia($mi, $property);
            }
        }

        $alData = [
            'member_id' => $data['member_id'],
            'allowanceable_type' => EducationSchemeApplication::class,
            'allowanceable_id' => $esa->id,
            'application_no' => AppHelper::getWelfareApplicationNumber($member, $data['scheme_code']),
            'application_date' => Carbon::today()->format('Y-m-d'),
            'welfare_scheme_id' => WelfareScheme::where('code', $data['scheme_code'])->get()->first()->id,
        ];
        $allowance = Allowance::create($alData);
        AllowanceEvent::dispatch($member->district_id, 'created', $allowance);
        return $allowance;
    }

    public function approve($id, $approval, $amount = null)
    {
        try {
            $status = match($approval) {
                'Approved' => Allowance::$STATUS_APPROVED,
                'Rejected' => Allowance::$STATUS_REJECTED,
            };
            $a = Allowance::find($id);
            $a->status = $status;
            if ($amount != null) {
                $a->sanctioned_amount = $amount;
                $a->sanctioned_date = Carbon::today()->format('Y-m-d');
            }
            $a->save();
            $a->refresh();
            AllowanceEvent::dispatch($a->member->district_id, 'approved', $a);
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
        $uid = auth()->user()->district_id;
        $ps = Allowance::$STATUS_PENDING;
        return Allowance::with('member')
            ->where('status', $ps)
            ->whereHas(
                'member', function($query) use ($uid) {
                    return $query->where('district_id', $uid);
                }
            )->get();
    }

}
