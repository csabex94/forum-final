<?php

namespace App\Repositories\Message;

interface MessageRepositoryInterface {
    public function addPrivateMessage($sender, $receiver, $message);
    public function addTopicConversation($sender, $team, $topic, $message);
    public function getPrivateMessage($id);
    public function getTeamMessage($id);
    public function unsendPrivateMessage($id);
    public function unsendTeamMessage($id);
}   