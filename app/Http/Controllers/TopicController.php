<?php

namespace App\Http\Controllers;

use App\Events\NewTopicCreatedEvent;
use App\Models\Team;
use App\Models\Topic;
use App\Models\TopicConversation;
use App\Models\TopicOpened;
use App\Models\TopicPermission;
use App\Models\TopicRead;
use App\Notifications\NewTopicNotification;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Repositories\Topic\TopicRepositoryInterface;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class TopicController extends Controller {

    protected $topic, $message;

    public function __construct(TopicRepositoryInterface $topic, MessageRepositoryInterface $message) {
        $this->topic = $topic;
        $this->message = $message;
    }

    public function store(Request $request): \Inertia\Response {
        $validData = $request->validate([
            'title' => 'required|string',
            'team_id' => 'required|integer',
            'permission' => 'required|string'
        ]);
        // Creating/Saving new Topic
        $newTopic = new Topic();
        $newTopic->permission = $validData['permission'];
        $newTopic->title = $validData['title'];
        $newTopic->team_id = $validData['team_id'];
        $newTopic->user_id = $request->user()->id;
        if (isset($request->description) && $request->description) {
            $request->validate([
                'description' => 'string'
            ]);

            $newTopic->description = $request->description;
        }
        $newTopic->save();
        // Create permission for every user from group if custom permission is selected
        if ($validData['permission'] === 'custom' && isset($request->userPermissions)) {
            foreach($request->userPermissions as $userPermission) {
                TopicPermission::create([
                    'topic_id' => $newTopic->id,
                    'team_id' => $validData['team_id'],
                    'user_id' => $userPermission['userId'],
                    'permission' => $userPermission['permission']
                ]);
            }
        }
        // Send notification to every user from group
        foreach ($request->user()->currentTeam->allUsers() as $user) {
            if ($user->id != $newTopic->user_id) {
                TopicOpened::create([
                    'topic_id' => $newTopic->id,
                    'team_id' => $validData['team_id'],
                    'user_id' => $user->id,
                    'status' => 'not-seen'
                ]);
                event(
                    new NewTopicCreatedEvent($newTopic->id, $user->id, $validData['team_id'])
                );
            }
        }

        // Create Log
        $user = $request->user();
        activity()
            ->causedBy($user)
            ->performedOn($newTopic)
            ->tap(function(Activity $activity) use ($user, $newTopic) {
                $activity->subject_id = $newTopic->id;
                $activity->causer_id = $user->id;
                $activity->log_name = 'created';
                $activity->description = 'Created Topic #'. $newTopic->id . ' - ' . $newTopic->title;
            })
            ->log('created');

        return Inertia::render('Home', [
            'currentTopics' => Topic::where('team_id', $request->team_id)->with('createdBy', 'userPermissions')->orderBy('created_at', 'DESC')->limit(5)->get(),
            'rightSide' => 'groups',
            'middleSection' => 'topics',
        ]);
    }

    public function update(Request $request): \Inertia\Response {
        $request->validate([
            'title' => 'string',
            'team_id' => 'required|integer',
            'topic_id' => 'required|integer',
            'permission' => 'required|string'
        ]);


        $topic =  Topic::where([['id', $request->topic_id], ['team_id', $request->team_id]])->first();
        $topic->title = $request->title;
        $topic->description = $request->description;
        $topic->permission = $request->permission;
        $topic->save();


        $user = $request->user();

        if ($request->permission != 'custom') {
            TopicPermission::where([
                ['team_id', $request->user()->current_team_id],
                ['topic_id', $topic->id]
            ])->delete();
        }

        if ($request->permission === 'custom' && isset($request->userPermissions)) {
            foreach($request->userPermissions as $userPermission) {
                $this->checkExistingPermissionOrCreate($userPermission['userId'], $request->user()->current_team_id, $topic->id, $userPermission['permission']);
            }
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($topic)
            ->tap(function(Activity $activity) use ($user, $topic) {
                $activity->subject_id = $topic->id;
                $activity->causer_id = $user->id;
                $activity->log_name = 'updated';
                $activity->description = 'Updated Topic #'. $topic->id . ' - ' . $topic->title;
            })
            ->log('updated');

        return Inertia::render('Home', [
            'currentTopics' => Topic::where('team_id', $request->team_id)->with('createdBy', 'userPermissions')->orderBy('created_at', 'DESC')->limit(5)->get(),
            'rightSide' => 'groups',
            'middleSection' => 'topics',
        ]);
    }

    public function checkExistingPermissionOrCreate($userId, $teamId, $topicId, $userPermission)
    {
        $permission = TopicPermission::where([
            ['user_id', $userId],
            ['team_id', $teamId],
            ['topic_id', $topicId]
        ])->first();
        if ($permission) {
            $permission->permission = $userPermission;
            $permission->save();
        } else {
            TopicPermission::create([
                'user_id' => $userId,
                'team_id' => $teamId,
                'topic_id' => $topicId,
                'permission' => $userPermission
            ]);
        }
    }

    public function storeTopicFileConversation(Request $request): \Inertia\Response {
        return $this->topic->storeTopicFileConversation($request);
    }

    public function updateGroupPhoto(Request $request) {
        return $this->topic->updateGroupPhoto($request);
    }

    public function deleteTopic(Request $request): \Inertia\Response {
        $topic = Topic::where('id', $request->topicId)->with(['createdBy', 'topicConversations', 'topicConversations.topicReads', 'topicOpeneds', 'userPermissions', 'files'])->first();

        $user = $request->user();
        activity()
            ->causedBy($user)
            ->performedOn($topic)
            ->tap(function(Activity $activity) use ($user, $topic) {
                $activity->subject_id = $topic->id;
                $activity->causer_id = $user->id;
                $activity->log_name = 'deleted';
                $activity->description = 'Deleted Topic #'. $topic->id . ' - ' . $topic->title;
            })
            ->log('deleted');


        foreach($topic->files as $file) {
            Storage::disk('local')->delete('public/'.$file->filename);
            $file->delete();
        }

        TopicConversation::where('topic_id', $topic->id)->delete();
        TopicPermission::where('topic_id', $topic->id)->delete();
        TopicOpened::where('topic_id', $topic->id)->delete();

        $topic->delete();

        return Inertia::render('Home', [
            'currentTopics' => Topic::where('team_id', $request->user()->current_team_id)->with('createdBy', 'userPermissions')->orderBy('created_at', 'DESC')->limit(5)->get(),
            'rightSide' => 'groups',
            'middleSection' => 'topics',
        ]);
    }

    public function paginateTopics(Request $request): \Illuminate\Http\JsonResponse
    {
        $page = $request->page;
        $perPage = 5;
        $skip = (($page - 1) * $perPage);
        $currentTopics = Topic::where('team_id', $request->user()->current_team_id)
            ->with('createdBy', 'userPermissions')
            ->limit($perPage)
            ->skip($skip)
            ->latest()
            ->get()
            ->toArray();
        return response()->json($currentTopics, 200);
    }
}
