<?php

namespace App\Repositories\Topic;

interface TopicRepositoryInterface {
    public function getTopic($topicId);
    public function getTopics($team_id);
    public function storeTopicConversation($request);
    public function storeTopicFileConversation($request);
    public function createTopic($request);
    public function updateTopic($request);
    public function deleteTopic($topic_id, $delete_files);
    public function createTopicFile($file_id, $topic_id);
    public function deleteTopicFile($topic_file_id);
    public function updateGroupPhoto($request);
}
