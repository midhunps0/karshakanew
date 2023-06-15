<?php

namespace App\Listeners;

use App\Models\District;
use App\Events\AllowanceEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AllowanceEventListener
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
    public function handle(AllowanceEvent $event): void
    {
        info('appln event captured: ' . $event->action);
        switch ($event->action) {
            case 'created':
                $d = District::find($event->districtId);
                $d->last_application_no = $d->last_application_no + 1;
                $d->pending_applications = $d->pending_applications + 1;
                $d->save();
                break;
            case 'approved':
                $d = District::find($event->districtId);
                $d->pending_applications = $d->pending_applications - 1;
                $d->save();
                break;
        }
    }
}
