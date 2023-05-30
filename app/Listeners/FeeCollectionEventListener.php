<?php

namespace App\Listeners;

use App\Events\FeeCollectionEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        //
    }
}
