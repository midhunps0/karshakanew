<?php

namespace App\Listeners;

use App\Models\District;
use App\Events\FeeCollectionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeeCollectionEventListener
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
    public function handle(FeeCollectionEvent $event): void
    {
        switch ($event->action) {
            case FeeCollectionEvent::$ACTION_CREATED:
                $d = District::find($event->districtId);
                $rno_arr = explode('/', $event->feeCollection->receipt_number);
                $n = array_pop($rno_arr);
                $d->last_receipt_no = intval($n);
                $d->last_receipt_date = $event->feeCollection->receipt_date;
                $d->save();
                break;
        }
    }
}
