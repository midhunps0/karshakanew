<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use App\Models\District;
use Illuminate\Http\Request;
use App\Services\MemberService;
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

        return $this->buildResponse('dashboard', $data);
    }
}
