<?php

namespace App\Observers;

use App\Events\MemberCountEvent;
use App\Events\MemberStatusEvent;
use App\Models\Member;

class MemberObserver
{
    /**
     * Handle the Member "created" event.
     */
    public function created(Member $member): void
    {
        //
    }

    /**
     * Handle the Member "updated" event.
     */
    public function updating(Member $member): void
    {
        if ($member->isDirty('active')){
            if ($member->active) {
                info("Member id: $member->id active status changed to active");
                MemberStatusEvent::dispatch($member, MemberStatusEvent::$ACTION_ENABLED);
            } else {
                info("Member id: $member->id active status changed to inactive");
                MemberStatusEvent::dispatch($member, MemberStatusEvent::$ACTION_DISABLED);
            }
        }
    }

    /**
     * Handle the Member "deleted" event.
     */
    public function deleted(Member $member): void
    {
        MemberCountEvent::dispatch($member, MemberCountEvent::$ACTION_DELETED);
    }

    /**
     * Handle the Member "restored" event.
     */
    public function restored(Member $member): void
    {
        //
    }

    /**
     * Handle the Member "force deleted" event.
     */
    public function forceDeleted(Member $member): void
    {
        //
    }
}
