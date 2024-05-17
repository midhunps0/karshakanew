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
use App\Models\DeathExgraciaApplication;
use Ynotz\MediaManager\Models\MediaItem;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PostDeathAllowanceService
{
    public function store($data)
    {
        $member = Member::find($data['member_id']);
        $applnData = collect($this->prepareForStoreValidation($data))->only([
            'member_id',
            'member_name',
            'member_address',
            'date_of_death',
            'marital_status',
            // 'application_date',
            'member_reg_date',
            'applicant_name',
            'applicant_address',
            'applicant_phone',
            'applicant_aadhaar',
            'applicant_bank_details',
            'applicant_relation',
            'applicant_is_minor',
        ])->toArray();
        $applnData['member_reg_date'] = AppHelper::formatDateForSave($member->reg_date);
        $applnData['date_of_death'] = AppHelper::formatDateForSave($applnData['date_of_death']);
        DB::beginTransaction();
        /**
         * @var DeathExgraciaApplication
         */
        $esa = DeathExgraciaApplication::create($applnData);
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_front', $data);
        AppHelper::syncImageFromRequestData($esa, 'wb_passbook_back', $data);
        AppHelper::syncImageFromRequestData($esa, 'aadhaar_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'bank_passbook', $data);
        AppHelper::syncImageFromRequestData($esa, 'ration_card', $data);
        AppHelper::syncImageFromRequestData($esa, 'one_and_same_certificate', $data);
        AppHelper::syncImageFromRequestData($esa, 'minor_age_proof', $data);
        AppHelper::syncImageFromRequestData($esa, 'death_certificate', $data);

        BusinessActionEvent::dispatch(
            DeathExgraciaApplication::class,
            $esa->id,
            'Created',
            auth()->user()->id,
            null,
            $esa,
            'Created DeathExgraciaApplication with id: '.$esa->id,
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
            'allowanceable_type' => DeathExgraciaApplication::class,
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
         * @var DeathExgraciaApplication
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
            DeathExgraciaApplication::class,
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
            'allowanceable_type' => DeathExgraciaApplication::class,
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

    public function prepareForStoreValidation(array $data): array
    {
        return $data;
    }

}
