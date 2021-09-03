<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class InviteWasAcceptedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message, $userId, $teamId, $teamType;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(String $message, $userId, $teamId, String $teamType)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->teamType = $teamType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        $userId = $this->userId;
        return new PrivateChannel("user-$userId");
    }

    public function broadcastAs(): String
    {
        return "invite-was-accepted-event";
    }
}
