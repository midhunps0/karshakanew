<?php

namespace App\Events;

use App\Models\Accounting\Transaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TransactionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    static $ACTION_CREATED = 'created';
    static $ACTION_DELETED = 'deleted';
    static $ACTION_UPDATED = 'updated';

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $districtId,
        public Transaction $transaction,
        public string $action
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    // public function broadcastOn(): array
    // {
    //     return [
    //         new PrivateChannel('channel-name'),
    //     ];
    // }
}
