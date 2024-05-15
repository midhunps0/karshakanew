<?php

namespace App\Listeners;

use App\Models\District;
use App\Helpers\AppHelper;
use App\Events\AllowanceEvent;
use Illuminate\Support\Carbon;
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
        switch ($event->action) {
            case AllowanceEvent::$ACTION_CREATED:
                $d = District::find($event->districtId);
                $ano_arr = explode('/', $event->allowance->application_no);
                $n= array_pop($ano_arr);
                $d->last_application_no = $n;
                $dtstr = explode(' ', $event->allowance->created_at)[0];
                $d->last_application_date = Carbon::createFromFormat(
                    'Y-m-d',
                    $dtstr
                );
                $d->pending_applications = $d->pending_applications + 1;
                $d->save();
                $this->setLastApplicationNo($event->allowance, $d);
                break;
            case AllowanceEvent::$ACTION_APPROVED:
                $d = District::find($event->districtId);
                $d->pending_applications = $d->pending_applications == 0 ? 0: $d->pending_applications - 1;
                $d->save();
                break;
            case AllowanceEvent::$ACTION_DELETED:
                $d = District::find($event->districtId);
                $d->pending_applications = $d->pending_applications == 0 ? 0: $d->pending_applications - 1;
                $d->save();
                break;
        }
    }

    public function setLastApplicationNo($allowance, $district)
    {
        # code...
    }
}
