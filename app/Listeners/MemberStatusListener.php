<?php

namespace App\Listeners;

use App\Events\MemberStatusEvent;
use App\Models\District;
use App\Models\Taluk;
use App\Models\Village;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MemberStatusListener
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
    public function handle(MemberStatusEvent $e): void
    {
        $district = District::find($e->member->district_id);
        $taluk = Taluk::find($e->member->taluk_id);
        $village = Village::find($e->member->village_id);
        switch ($e->action) {
            case MemberStatusEvent::$ACTION_ENABLED:
                $district->active_members = $district->active_members + 1;
                $district->inactive_members = $district->inactive_members - 1;
                $district->save();
                $taluk->active_members = $taluk->active_members + 1;
                $taluk->inactive_members = $taluk->inactive_members - 1;
                $taluk->save();
                $village->active_members = $village->active_members + 1;
                $village->inactive_members = $village->inactive_members - 1;
                $village->save();
                break;
            case MemberStatusEvent::$ACTION_DISABLED:
                $district->active_members = $district->active_members - 1;
                $district->inactive_members = $district->inactive_members + 1;
                $district->save();
                $taluk->active_members = $taluk->active_members - 1;
                $taluk->inactive_members = $taluk->inactive_members + 1;
                $taluk->save();
                $village->active_members = $village->active_members - 1;
                $village->inactive_members = $village->inactive_members + 1;
                $village->save();
                break;
        }
    }
}
