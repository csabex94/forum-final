<?php

namespace App\Repositories\Message;

use App\Models\PrivateMessage;
use App\Models\TeamMessage;
use App\Models\TopicConversation;
use Illuminate\Support\Facades\Storage;

class MessageRepository implements MessageRepositoryInterface {

    protected $pm, $tm;

    public function __construct(PrivateMessage $pm, TopicConversation $tm) {
        $this->pm = $pm;
        $this->tm = $tm;
    }

    public function getPrivateMessage($id) {
        return $this->pm->find($id);
    }

    public function getTeamMessage($id) {
        return $this->tm->find($id);
    }

    public function addPrivateMessage($sender, $receiver, $message) {
        $message = new PrivateMessage();

        $message->user_id = $sender;
        $message->receiver_id = $receiver;
        $message->message = $message;

        $message->save();

        return $message;
    }

    public function addTopicConversation($sender, $team, $topic, $message) {
        $message = new TopicConversation();

        $message->user_id = $sender;
        $message->team_id = $team;
        $message->topic_id = $topic;
        $message->message = $message;

        $message->save();

        return $message;
    }

    public function deletePrivateMessage($id) {
        $message = $this->pm->find($id);
        $message->delete();

        return true;
    }

    public function deleteTeamMessage($id) {
        $message = $this->tm->find($id);
        $message->delete();

        return true;
    }

    public function unsendPrivateMessage($id) {
        $message = $this->getPrivateMessage($id);
        if($message->file) {
            Storage::delete('files', $message->file->filename);
            $message->file_id = NULL;
        }
        $message->message = 'This message was removed.';
        $message->save();

        return $message;
    }

    public function unsendTeamMessage($id) {
        $message = $this->getTeamMessage($id);
        if($message->file) {
            Storage::delete('files', $message->file->filename);
            $message->file_id = NULL;
        }
        $message->message = 'This message was removed.';
        $message->save();

        return $message;
    }

}
