<?php

namespace App\Providers;

// use Illuminate\Auth\Events\Registered;
// use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

use App\Events\MemberEvent;
use App\Events\AllowanceEvent;
use App\Events\FeeCollectionEvent;
use App\Events\BusinessActionEvent;
use App\Events\TransactionEvent;
use Illuminate\Support\Facades\Event;
use App\Listeners\MemberEventListener;
use App\Listeners\AllowanceEventListener;
use App\Listeners\FeeCollectionEventListener;
use App\Listeners\BusinessActionEventListener;
use App\Listeners\TransactionEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        BusinessActionEvent::class => [
            BusinessActionEventListener::class
        ],
        FeeCollectionEvent::class => [
            FeeCollectionEventListener::class
        ],
        MemberEvent::class => [
            MemberEventListener::class
        ],
        AllowanceEvent::class => [
            AllowanceEventListener::class
        ],
        TransactionEvent::class => [
            TransactionEventListener::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
