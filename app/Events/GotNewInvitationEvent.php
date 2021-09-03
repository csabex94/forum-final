<?php

namespace App\Events;

use App\Models\TeamInvitation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GotNewInvitationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $invitation;
    public $userId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($teamInvitation, $userId)
    {
        $this->invitation = $teamInvitation;
        $this->userId = $userId;
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
        return "new-invitation";
    }

    public function broadcastWith(): array
    {
        return [
            'invitation' => $this->invitation->load('team', 'team.owner')
        ];
    }
}
