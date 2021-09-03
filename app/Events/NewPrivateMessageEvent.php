<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use App\Models\PrivateMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPrivateMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $privateMessage;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PrivateMessage $privateMessage, $userId)
    {
        $this->privateMessage = $privateMessage;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        $userId = $this->userId;
        return new PrivateChannel("user-$userId");
    }

    public function broadcastAs(): string
    {
        return 'new-message';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->privateMessage,
            'type' => 'private'
        ];
    }
}
