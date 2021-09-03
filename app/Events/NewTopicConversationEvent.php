<?php

namespace App\Events;

use App\Models\TopicConversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTopicConversationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $topicConversation, $topic;
    public $userId, $topicId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(TopicConversation $topicConversation, $userId, $topicId, $topic)
    {
        $this->topicConversation = $topicConversation;
        $this->userId = $userId;
        $this->topicId = $topicId;
        $this->topic = $topic;
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
            'message' => $this->topicConversation->load('team'),
            'type' => 'group',
            'topicId' => $this->topicId,
            'topic' => $this->topic
        ];
    }
}
