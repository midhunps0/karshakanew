<?php

namespace App\Listeners;

use App\Events\MemberCountEvent;
use App\Models\District;
use App\Models\Taluk;
use App\Models\Village;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MemberCountListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MemberCountEvent $e):void
    {
        $district = District::find($e->member->district_id);
        $taluk = Taluk::find($e->member->taluk_id);
        $village = Village::find($e->member->village_id);
        switch ($e->action) {
            case MemberCountEvent::$ACTION_CREATED:
                $district->total_applied_members = $district->total_applied_members + 1;
                $district->save();
                $taluk->total_applied_members = $taluk->total_applied_members + 1;
                $taluk->save();
                $village->total_applied_members = $village->total_applied_members + 1;
                $village->save();
                break;
            case MemberCountEvent::$ACTION_TRANSFER_IN:
                $district->total_applied_members = $district->total_applied_members + 1;
                $district->save();
                $taluk->total_applied_members = $taluk->total_applied_members + 1;
                $taluk->save();
                $village->total_applied_members = $village->total_applied_members + 1;
                $village->save();
                break;
            case MemberCountEvent::$ACTION_APPROVED:
                $district->total_applied_members = $district->total_approved_members + 1;
                $district->save();
                $taluk->total_applied_members = $taluk->total_approved_members + 1;
                $taluk->save();
                $village->total_applied_members = $village->total_approved_members + 1;
                $village->save();
                break;
            case MemberCountEvent::$ACTION_DELETED:
                $district->total_applied_members = $district->total_applied_members - 1;
                $taluk->total_applied_members = $taluk->total_applied_members - 1;
                $village->total_applied_members = $village->total_applied_members - 1;
                if ($e->member->active) {
                    $district->active_members = $district->active_members - 1;
                    $taluk->active_members = $taluk->active_members - 1;
                    $village->active_members = $village->active_members - 1;
                } else {
                    $district->inactive_members = $district->active_members - 1;
                    $taluk->inactive_members = $taluk->active_members - 1;
                    $village->inactive_members = $village->active_members - 1;
                }
                $district->save();
                $taluk->save();
                $village->save();
                break;
            case MemberCountEvent::$ACTION_TRANSFER_OUT:
                $district->total_applied_members = $district->total_applied_members - 1;
                $district->save();
                $taluk->total_applied_members = $taluk->total_applied_members - 1;
                $taluk->save();
                $village->total_applied_members = $village->total_applied_members - 1;
                $village->save();
                break;
        }
    }
}
