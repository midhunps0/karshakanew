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

        /*
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
        $monthStart = Carbon::today()->startOfMonth();
        $data['new_registrations'] = Member::userAccessControlled()
            ->where('created_at', '>=', $monthStart)->count();
        $data['active_members'] = Member::userAccessControlled()
            ->where('active', 1)->count();
        return $this->buildResponse('dashboard', $data);
*/

            $data['unapproved_members'] = 0;
            $data['show_unapproved'] = 0;
            $data['pending_applications'] = 0;
            $data['transfer_requests'] = 0;
            $data['new_registrations'] = 0;
            $data['active_members'] = 0;

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

    public function dashboardAllowancesData(Request $request, DashboardService $ds)
    {
        try {
            return response()->json(
                $ds->dashboardAllowancesData(
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
