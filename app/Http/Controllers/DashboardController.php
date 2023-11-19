<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use App\Models\District;
use Illuminate\Http\Request;
use App\Models\MemberTransfer;
use Illuminate\Support\Carbon;
use App\Services\MemberService;
use App\Services\DashboardService;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class DashboardController extends SmartController
{
    public function dashboard()
    {
        $data = [];
        /** @var User */
        $user = User::find(auth()->user()->id);
        $data['show_unapproved'] = false;
        $data['show_pending_applications'] = false;
        $data['to'] = Carbon::now()->format('d-m-Y');
        $data['from'] = Carbon::now()->startOfMonth()->format('d-m-Y');
        if ($user->hasPermissionTo('Member: Approve In Own District') ) {
            $data['unapproved_members'] = Member::userAccessControlled()->unapproved()->count();
            $data['show_unapproved'] = true;
        }
        if ($user->hasPermissionTo('Allowance: Approve In Own District')
            || $user->hasPermissionTo('Allowance: Approve In Any District')
        ) {
            $data['pending_applications'] = (District::find($user->district_id))->pending_applications;
            $data['show_pending_applications'] = true;
        }

        if($user->hasPermissionTo('Member Transfer: Edit In Own District')) {
            $data['transfer_requests'] = MemberTransfer::requestsReceived()->count();
        }
        return $this->buildResponse('dashboard', $data);
    }

    public function dashboardData(Request $request, DashboardService $ds)
    {
        try {
            return response()->json(
                $ds->dashboardData(
                    $request->input('from'),
                    $request->input('to'),
                )
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'success' => false,
                    'error' => $e->__toString()
                ]
            );
        }

    }
}
