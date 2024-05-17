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
use Ynotz\MediaManager\Models\MediaItem;
use App\Models\SuperAnnuationApplication;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class SuperAnnuationService
{
    public function store($data)
    {
        $member = Member::find($data['member_id']);
        $applnData = collect($this->prepareForStoreValidation($data))->only([
            'member_id',
            'member_name',
            'member_address',
            'member_reg_no',
            'member_reg_date',
            'member_phone',
            'member_aadhaar',
            'member_dob',
            'member_age',
            'member_bank_account',
            'fee_period_from',
            'fee_period_to',
            'arrear_months',
        ])->toArray();
        $applnData['member_reg_no'] = $member->membership_no;
        $applnData['member_reg_date'] = $member->reg_date;
        $applnData['fee_period_from'] = AppHelper::formatDateForSave($applnData['fee_period_from']);
        $applnData['fee_period_to'] = AppHelper::formatDateForSave($applnData['fee_period_to']);
        $applnData['member_dob'] = AppHelper::formatDateForSave($applnData['member_dob']);

        DB::beginTransaction();
        /**
         * @var SuperAnnuationApplication
         */
        $esa = SuperAnnuationApplication::create($applnData);
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_front', $data);
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_back', $data);
        AppHelper::syncImageFromRequestData($esa, 'aadhaar_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'bank_passbook', $data);
        AppHelper::syncImageFromRequestData($esa, 'ration_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'union_certificate', $data);
        AppHelper::syncImageFromRequestData($esa, 'one_and_same_certificate', $data);

        BusinessActionEvent::dispatch(
            SuperAnnuationApplication::class,
            $esa->id,
            'Created',
            auth()->user()->id,
            null,
            $esa,
            'Created SuperAnnuationApplication with id: '.$esa->id,
            $member->district_id
        );

        if (isset($data['existing'])) {
            foreach ($data['existing'] as $property => $ulid) {
                $mi = MediaItem::where('ulid', $ulid)->get()->first();
                $esa->attachMedia($mi, $property);
            }
        }

        $applNo = $data['application_no'] != null && strlen(trim($data['application_no'])) > 0 ? $data['application_no'] : AppHelper::getWelfareApplicationNumber($member, $data['scheme_code']);

        $alData = [
            'member_id' => $data['member_id'],
            'district_id' => $member->district_id,
            'allowanceable_type' => SuperAnnuationApplication::class,
            'allowanceable_id' => $esa->id,
            'application_no' => $applNo,
            'application_date' => AppHelper::formatDateForSave($data['application_date']),
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
        DB::commit();
        return $allowance;
    }

    public function update($id, $data)
    {
        /**
         * @var Allowance
         */
        $allowance = Allowance::find($id);

        /**
         * @var SuperAnnuationApplication
         */
        $esa = $allowance->allowanceable;
        $member = $allowance->member;
        $applnData = collect($this->prepareForStoreValidation($data))->only([
            'member_id',
            'member_name',
            'member_address',
            'member_reg_no',
            'member_reg_date',
            'member_phone',
            'member_aadhaar',
            'member_dob',
            'member_age',
            'member_bank_account',
            'fee_period_from',
            'fee_period_to',
            'arrear_months',
        ])->toArray();

        $applnData['member_reg_no'] = $member->membership_no;
        $applnData['member_reg_date'] = $member->reg_date;
        $applnData['fee_period_from'] = AppHelper::formatDateForSave($applnData['fee_period_from']);
        $applnData['fee_period_to'] = AppHelper::formatDateForSave($applnData['fee_period_to']);
        $applnData['member_dob'] = AppHelper::formatDateForSave($applnData['member_dob']);

        DB::beginTransaction();
        $esa->update($applnData);
        $esa->save();
        $esa->refresh();
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_front', $data);
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_back', $data);
        AppHelper::syncImageFromRequestData($esa, 'aadhaar_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'bank_passbook', $data);
        AppHelper::syncImageFromRequestData($esa, 'ration_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'union_certificate', $data);
        AppHelper::syncImageFromRequestData($esa, 'one_and_same_certificate', $data);

        BusinessActionEvent::dispatch(
            SuperAnnuationApplication::class,
            $esa->id,
            'Updated',
            auth()->user()->id,
            null,
            $esa,
            'Updated SuperAnnuationApplication with id: '.$esa->id,
            $member->district_id
        );

        if (isset($data['existing'])) {
            foreach ($data['existing'] as $property => $ulid) {
                $mi = MediaItem::where('ulid', $ulid)->get()->first();
                $esa->attachMedia($mi, $property);
            }
        }

        $alData = [
            'member_id' => $member->id,
            'district_id' => $member->district_id,
            'allowanceable_type' => SuperAnnuationApplication::class,
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
            'Updated Medical Allowance with id: '.$allowance->id,
            $member->district_id
        );
        DB::commit();
        return $allowance;
    }

    public function prepareForStoreValidation(array $data): array
    {
        return $data;
    }

}
