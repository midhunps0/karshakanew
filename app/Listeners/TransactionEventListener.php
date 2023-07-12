<?php

namespace App\Listeners;

use App\Models\District;
use App\Events\TransactionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransactionEventListener
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
    public function handle(TransactionEvent $event): void
    { info('transaction event captured');
        switch ($event->action) {
            case TransactionEvent::$ACTION_CREATED:
                $d = District::find($event->districtId);
                $rno_arr = explode('/', $event->transaction->receipt_voucher_no);
                $n = array_pop($rno_arr);
                switch($event->transaction->type) {
                    case 'receipt':
                        $d->last_gen_receipt_no = intval($n);
                        $d->last_gen_receipt_date = $event->transaction->date;
                        break;
                    case 'voucher':
                        $d->last_gen_voucher_no = intval($n);
                        $d->last_gen_voucher_date = $event->transaction->date;
                        break;
                }

                $d->save();
                break;
        }
    }
}
