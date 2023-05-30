<?php

namespace App\Listeners;

use App\Events\BusinessActionEvent;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BusinessActionEventListener
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
    public function handle(BusinessActionEvent $event): void
    {
        AuditLog::create([
            'auditable_type' => $event->modelType,
            'auditable_id' => $event->modelId,
            'old_value' => $event->oldValue,
            'new_value' => $event->newValue,
            'description' => $event->description,
        ]);
    }
}