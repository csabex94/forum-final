<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTopicNotification extends Notification
{
    use Queueable;
    public $topic, $team, $userId;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($topic, $team, $userId)
    {
        $this->topic = $topic;
        $this->team = $team;
        $this->userId = $userId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'topic' => $this->topic,
            'userId' => $this->userId,
            'team' => $this->team
        ];
    }
}
