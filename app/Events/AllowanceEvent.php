<?php

namespace App\Events;

use App\Models\Allowance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AllowanceEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    static $ACTION_CREATED = 'created';
    static $ACTION_UPDATED = 'updated';
    static $ACTION_APPROVED = 'approved';
    static $ACTION_DELETED = 'deleted';
    /**
     * Create a new event instance.
     */
    public function __construct(public int $districtId, public string $action, public Allowance|null $allowance = null)
    {}

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
