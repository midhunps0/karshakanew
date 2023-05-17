<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class DashboardController extends SmartController
{
    public function dashboard()
    {
        $data = [];
        /** @var User */
        $user = User::find(auth()->user()->id);
        $data['show_unapproved'] = false;
        if ($user->hasPermissionTo('Member: Approve In Own District') ) {
            $data['unapproved_members'] = Member::userAccessControlled()->unapproved()->count();
            $data['show_unapproved'] = true;
        }

        return $this->buildResponse('dashboard', $data);
    }
}
