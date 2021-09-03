<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTopicCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $topicId, $userId, $teamId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($topicId, $userId, $teamId)
    {
        $this->topicId = $topicId;
        $this->userId = $userId;
        $this->teamId = $teamId;
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

    public function broadcastAs(): string
    {
        return "new-topic-created";
    }
}
