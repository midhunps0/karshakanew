<?php

namespace App\Events;

use App\Models\FeeCollection;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FeeCollectionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    static $ACTION_CREATED = 'created';
    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $districtId,
        public FeeCollection $feeCollection,
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
