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
use App\Models\MaternityAssistanceApplication;
use Illuminate\Contracts\Database\Query\Builder;

class MaternityAssistanceService
{
    public function store($data)
    {
        $member = Member::find($data['member_id']);
        $applnData = collect($this->prepareForStoreValidation($data))->only([
            'member_id',
            'member_name',
            'member_address',
            'member_aadhaar',
            'member_reg_date',
            // 'member_reg_no',
            'fee_period_from',
            'fee_period_to',
            'arrear_months_dlrydt',
            'member_phone',
            'member_aadhaar',
            'member_bank_account',
            'delivery_date',
            'delivery_count',
            'relation',
            'history_count'
        ])->toArray();
        $applnData['member_reg_no'] = $member->membership_no;
        $applnData['member_reg_date'] = $member->reg_date;
        $applnData['delivery_date'] = AppHelper::formatDateForSave($applnData['delivery_date']);
        $applnData['fee_period_from'] = AppHelper::formatDateForSave($applnData['fee_period_from']);
        $applnData['fee_period_to'] = AppHelper::formatDateForSave($applnData['fee_period_to']);
        /**
         *
         */
        $esa = MaternityAssistanceApplication::create($applnData);
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_front', $data);
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_back', $data);
        AppHelper::syncImageFromRequestData($esa, 'aadhaar_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'bank_passbook', $data);
        AppHelper::syncImageFromRequestData($esa, 'ration_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'union_certificate', $data);
        AppHelper::syncImageFromRequestData($esa, 'one_and_same_certificate', $data);
        AppHelper::syncImageFromRequestData($esa, 'birth_certificate', $data);

        BusinessActionEvent::dispatch(
            MaternityAssistanceApplication::class,
            $esa->id,
            'Created',
            auth()->user()->id,
            null,
            $esa,
            'Created Maternity Assistance Application with id: '.$esa->id,
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
            'allowanceable_type' => MaternityAssistanceApplication::class,
            'allowanceable_id' => $esa->id,
            'application_no' => AppHelper::getWelfareApplicationNumber($member, $data['scheme_code']),
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
        return $allowance;
    }

    public function update($id, $data)
    {
        /**
         * @var Allowance
         */
        $allowance = Allowance::find($id);

        /**
         * @var MaternityAssistanceApplication
         */
        $esa = $allowance->allowanceable;
        $member = $allowance->member;
        $applnData = collect($this->prepareForStoreValidation($data))->only([
            'member_name',
            'member_address',
            'member_aadhaar',
            'member_reg_date',
            'fee_period_from',
            'fee_period_to',
            'arrear_months_dlrydt',
            'member_phone',
            'member_aadhaar',
            'member_bank_account',
            'delivery_date',
            'delivery_count',
            'relation',
            'history_count'
        ])->toArray();

        $applnData['member_id'] = $member->id;
        $applnData['member_reg_no'] = $member->membership_no;
        $applnData['member_reg_date'] = $member->reg_date;
        $applnData['delivery_date'] = AppHelper::formatDateForSave($applnData['delivery_date']);
        $applnData['fee_period_from'] = AppHelper::formatDateForSave($applnData['fee_period_from']);
        $applnData['fee_period_to'] = AppHelper::formatDateForSave($applnData['fee_period_to']);

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
        AppHelper::syncImageFromRequestData($esa, 'birth_certificate', $data);

        BusinessActionEvent::dispatch(
            MaternityAssistanceApplication::class,
            $esa->id,
            'Updated',
            auth()->user()->id,
            null,
            $esa,
            'Updated MaternityAssistanceApplication with id: '.$esa->id,
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
            'allowanceable_type' => MaternityAssistanceApplication::class,
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
        return $allowance;
    }

    public function prepareForStoreValidation(array $data): array
    {
        return $data;
    }

}
