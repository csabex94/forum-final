<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $userId, $teamId, $message, $createdAt, $unreadMessages;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $teamId, String $message, $createdAt, $unreadMessages)
    {
        $this->userId = $userId;
        $this->teamId = $teamId;
        $this->message = $message;
        $this->createdAt = $createdAt;
        $this->unreadMessages = $unreadMessages;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn(): array
    {
        $userId = $this->userId;
        return ["user-main-channel-$userId"];
    }
    public function broadcastAs(): string
    {
        return 'new-user-message';
    }
    public function broadcastWith(): array
    {
        return [
          'teamId' => $this->teamId,
          'message' => $this->message,
          'userId' => $this->userId,
          'createdAt' => $this->createdAt,
          'unreadMessages' => $this->unreadMessages
        ];
    }
}
