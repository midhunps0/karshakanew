<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberCountEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    static $ACTION_CREATED = 'created';
    static $ACTION_UPDATED = 'updated';
    static $ACTION_APPROVED = 'approved';
    static $ACTION_DELETED = 'deleted';

    /**
     * Create a new event instance.
     */
    public function __construct(public $member, public $districtId)
    {
        //
    }

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
