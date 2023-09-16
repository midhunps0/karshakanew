<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\Taluk;
use App\Models\Member;
use App\Models\Village;
use App\Models\District;
use App\Models\MemberTransfer;
use Ynotz\EasyAdmin\Services\IndexTable;
use Symfony\Component\HttpFoundation\Request;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;
use Illuminate\Auth\Access\AuthorizationException;

class MemberTransferService implements ModelViewConnector {
    use IsModelViewConnector;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->modelClass = MemberTransfer::class;
        $this->indexTable = new IndexTable();
        $this->searchesMap = [
            'taluk' => 'taluk_id',
            'district' => 'district_id',
            'village' => 'village_id'
        ];
        $this->selectionEnabled = false;
        $this->exportsEnabled = false;
    }

    public function getTransferFormData($id)
    {
        $disctricts = District::where('short_code', '<>', 'HO')->get();
        $member = Member::find($id);
        return [
            'member' => $member,
            'districts' => $disctricts
        ];
    }

    public function getTransferEditFormData($id)
    {
        $transfer = MemberTransfer::find($id);
        $disctricts = District::where('short_code', '<>', 'HO')->get();
        // $member = Member::find($id);
        return [
            'transfer' => $transfer,
            'districts' => $disctricts,
            'taluks' => Taluk::where('district_id', $transfer->district_id)->get(),
            'villages' => Village::where('taluk_id', $transfer->taluk_id)->get(),
        ];
    }

    public function getStoreValidationRules(): array
    {
        return [
            'member_id' => ['required', 'integer'],
            'district' => ['required', 'integer'],
            'taluk' => ['required', 'integer'],
            'village' => ['required', 'integer'],
        ];
    }

    public function processBeforeStore($data)
    {
        $m = Member::find($data['member_id']);
        $data['from_district_id'] = $m->district_id;
        $data['from_taluk_id'] = $m->taluk_id;
        $data['from_village_id'] = $m->village_id;
        $data['district_id'] = $data['district'];
        unset($data['district']);
        $data['taluk_id'] = $data['taluk'];
        unset($data['taluk']);
        $data['village_id'] = $data['village'];
        unset($data['village']);
        $data['requestedby_id'] = auth()->user()->id;
        $data['request_date'] = Carbon::today()->format('Y-m-d');
        return $data;
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'member_id' => ['required', 'integer'],
            'district' => ['required', 'integer'],
            'taluk' => ['required', 'integer'],
            'village' => ['required', 'integer'],
        ];
    }

    public function processBeforeUpdate($data)
    {
        $m = Member::find($data['member_id']);
        $data['from_district_id'] = $m->district_id;
        $data['from_taluk_id'] = $m->taluk_id;
        $data['from_village_id'] = $m->villge_id;
        $data['district_id'] = $data['district'];
        unset($data['district']);
        $data['taluk_id'] = $data['taluk'];
        unset($data['taluk']);
        $data['village_id'] = $data['village'];
        unset($data['village']);
        $data['requestedby_id'] = auth()->user()->id;
        $data['request_date'] = Carbon::today()->format('Y-m-d');
        return $data;
    }

    public function requestsPlaced()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        if (!($user->hasPermissionTo('Member Transfer: View In Any District') || $user->hasPermissionTo('Member Transfer: View In Own District'))) {
            throw new AuthorizationException('User is not authorised to view this page.');
        }

        return MemberTransfer::requestsPlaced()->get();
    }

    public function requestsReceived()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        if (!($user->hasPermissionTo('Member Transfer: View In Any District') || $user->hasPermissionTo('Member Transfer: View In Own District'))) {
            throw new AuthorizationException('User is not authorised to view this page.');
        }

        return MemberTransfer::requestsReceived()->get();
    }

    public function requestsApproved()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();
        if (!($user->hasPermissionTo('Member Transfer: View In Any District') || $user->hasPermissionTo('Member Transfer: View In Own District'))) {
            throw new AuthorizationException('User is not authorised to view this page.');
        }

        return MemberTransfer::requestsApproved()->get();
    }

    public function approve($id)
    {
        $transfer = MemberTransfer::find($id);
        $member = Member::find($transfer->member->id);
        $member->district_id = $transfer->district_id;
        $member->taluk_id = $transfer->taluk_id;
        $member->village_id = $transfer->village_id;
        $member->save();
        $transfer->processedby_id = auth()->user()->id;
        $transfer->processed_date = Carbon::now()->format('Y-m-d');
        $transfer->save();
        $transfer->refresh();
        return $transfer;
    }

}
