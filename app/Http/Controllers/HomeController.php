<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\PrivateMessage;
use App\Models\PrivateRead;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TopicConversation;
use App\Models\TopicFile;
use App\Models\TopicOpened;
use App\Models\TopicPermission;
use App\Models\TopicRead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Topic;
use App\Models\User;
use App\Repositories\File\FileRepositoryInterface;
use Laravel\Jetstream\Jetstream;

class HomeController extends Controller {

    protected $file;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct() {
        $this->file = app()->make(FileRepositoryInterface::class);
    }

    public function index(Request $request)
    {
        $response = [];
        $currentRightSide = 'groups';
        $rightSideComponent = 'group';
        $topicName = NULL;
        $currentTopic = NULL;
        $invitationsGot = TeamInvitation::where('email', $request->user()->email)->with(['team.owner'])->get();
        $invitationsSent = TeamInvitation::where('email', '<>', $request->user()->email)->with(['team' => function($q) use ($request) {
            $q->where([
                ['user_id', $request->user()->id],
                ['team_type', 'private-message']
            ])->get();
        }])->get();

        $totalUnreadPrivateMessages = PrivateRead::where([
            ['user_id', $request->user()->id],
            ['wseen', NULL]
        ])->get();

        $totalUnreadTopicConversations = TopicRead::where([
            ['user_id', $request->user()->id],
            ['wseen', NULL]
        ])->get();

        if (isset($request->topicId)) {
            $response = TopicConversation::where([
                ['team_id', $request->user()->current_team_id],
                ['topic_id', $request->topicId]
            ])->with(['createdBy', 'seenBy', 'files'])->limit(15)->latest()->get()->toArray();
            $userPermission = NULL;
            $topic = Topic::where('id', $request->topicId)->with('userPermissions')->first();
            if ($topic) {
                if ($topic->user_id === $request->user()->id) {
                    $userPermission = 'public';
                } else {
                    if ($topic->permission === 'custom') {
                        $permission = TopicPermission::where([
                            ['user_id', $request->user()->id],
                            ['team_id', $request->user()->current_team_id],
                            ['topic_id', $request->topicId]
                        ])->first();
                        if ($permission) {
                            $userPermission = $permission->permission;
                        }
                    }
                    if ($topic->permission === 'readonly' || $topic->permission === 'public') {
                        $userPermission = $topic->permission;
                    }
                }
                $currentTopic = array_merge($topic->toArray(), array('userPermission' => $userPermission));
//                $cachedTopic = Redis::get('topic_'.$topic->id);
//                if ($cachedTopic) {
//                    $currentTopic = array_merge(json_decode($cachedTopic, true), [
//                        'userPermission' => $userPermission,
//                        'cached' => true
//                    ]);
//                } else {
//                    Redis::set('topic_'.$topic->id);
//                    $currentTopic = array_merge($topic->toArray(), array('userPermission' => $userPermission));
//                }
            } else {
                $currentTopic = NULL;
            }
        }

        if (isset($request->uso) && $request->uso === 'not-seen') {
            $this->setUserTopicOpenedStatus($request->user()->id, $request->topicId, $request->user()->current_team_id);
        }

        if (isset($request->tn)) {
            $topicName = $request->tn;
        }

        return Inertia::render('Home', [
            'rightSide' => $currentRightSide,
            'currentTopics' => $this->getCurrentTopics($request),
            'currentTopicConversations' => $response,
            'middleSection' => $request->topicId ? 'topic-conversations' : 'topics',
            'rightSideComponent' => $rightSideComponent,
            'topicName' => $topicName,
            'groupFiles' => $this->getGroupFiles($request),
            'groupMedia' => function () use ($request) {
                if ($request->user()->currentTeam && $request->user()->currentTeam->team_type === 'private-message') {
                    return File::where([['team_id', $request->user()->current_team_id], ['extension', 'jpg']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'png']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jpeg']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jfif']])
                        ->get();
                }
                if ($request->user()->currentTeam && $request->user()->currentTeam->team_type === 'group') {
                    return TopicFile::where([['team_id', $request->user()->current_team_id], ['extension', 'jpg']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'png']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jpeg']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jfif']])
                        ->get();
                }
                return [];
            },
            'privateMessages' => PrivateMessage::where('team_id', $request->user()->current_team_id)->with(['createdBy', 'seenBy', 'files'])->limit(15)->latest()->get()->toArray(),
            'currentTopic' => $currentTopic,
            'teams' => function () use ($request) {
                if ($request->user()->currentTeam && $request->user()->currentTeam->team_type === 'group') {
                    return $request->user()->allTeams()->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations');
                } else {
                    return NULL;
                }
            },
            'invitationGot' => $invitationsGot,
            'invitationSent' => function () use ($request, $invitationsSent) {
                $result = array();
                foreach ($invitationsSent->load('team') as $invitation) {
                    if ($invitation->team->team_type === 'private-message' && $invitation->team->user_id === $request->user()->id) {
                        array_push($result, array_merge($this->getUserByEmail($invitation->email)->toArray(), ['invitationId' => $invitation->id]));
                    }
                }
                return $result;
            },
            'totalUnreadPrivateMessages' => count($totalUnreadPrivateMessages),
            'totalUnreadTopicConversations' => count($totalUnreadTopicConversations),
            'unreadNotificationsCount' => count($request->user()->unreadNotifications)
        ]);
    }

    public function getGroupFiles($request)
    {
        if ($request->user()->currentTeam && $request->user()->currentTeam->team_type === 'private-message') {
            return File::where([['team_id', $request->user()->current_team_id], ['extension', 'pdf']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'xlsx']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'doc']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'rtf']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'txt']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'docx']])
                ->get();
        }
        if ($request->user()->currentTeam && $request->user()->currentTeam->team_type === 'group') {
            return TopicFile::where([['team_id', $request->user()->current_team_id], ['extension', 'pdf']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'xlsx']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'doc']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'rtf']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'txt']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'docx']])
                ->get();
        }
        return [];
    }

    public function getUserTopicOpenedStatus($userId, $topicId, $teamId)
    {
        $userStatus = TopicOpened::where([
            ['user_id', $userId],
            ['topic_id', $topicId],
            ['team_id', $teamId]
        ])->first();
        return $userStatus ? $userStatus->status : NULL;
    }

    public function setUserTopicOpenedStatus($userId, $topicId, $teamId)
    {
        TopicOpened::where([
            ['user_id', $userId],
            ['topic_id', $topicId],
            ['team_id', $teamId]
        ])->delete();
    }

    public function download(): \Symfony\Component\HttpFoundation\BinaryFileResponse {
        $filename = request()->route('filename');
        $file = $this->file->getByFileName($filename);

        $path = storage_path('app/public') . '/' . $filename;
        $mimetype = mime_content_type($path);
        if ($mimetype=='application/pdf') {
            return response()->file($path, ['Content-Disposition' => 'inline; filename="'.$file->original_filename.'"']);
        } else {
            return response()->download($path, $file->original_filename);
        }
    }


    public function downloadTopicFile(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = request()->route('filename');
        $file = TopicFile::where('filename', $filename)->first();

        $path = storage_path('/app/public') . '/' . $filename;
        $mimetype = mime_content_type($path);
        if ($mimetype=='application/pdf') {
            return response()->file($path, ['Content-Disposition' => 'inline; filename="'.$file->original_filename.'"']);
        } else {
            return response()->download($path, $file->original_filename);
        }
    }

    public function getUserByEmail(String $email) {
        return User::where('email', $email)->first();
    }

    public function privateMessagesPaginate(Request $request): \Illuminate\Http\JsonResponse
    {
        $perPage = 15;
        $skip = (($request->page - 1) *  $perPage);
        $privateMessages = PrivateMessage::where('team_id', $request->user()->current_team_id)
            ->with(['createdBy', 'seenBy', 'files'])
            ->limit($perPage)
            ->skip($skip)
            ->latest()
            ->get()
            ->toArray();
        return response()->json($privateMessages, 200);
    }

    public function topicConversationsPaginate(Request $request): \Illuminate\Http\JsonResponse
    {
        $perPage = 15;
        $skip = (($request->page - 1) *  $perPage);
        $topicConversations = TopicConversation::where([
            ['team_id', $request->user()->current_team_id],
            ['topic_id', $request->topicId]
        ])
            ->with(['createdBy', 'seenBy', 'files'])
            ->limit($perPage)
            ->skip($skip)
            ->latest()
            ->get()
            ->toArray();
        return response()->json($topicConversations, 200);
    }


    public function customSwitchTeam(Request $request)
    {
        $team = Jetstream::newTeamModel()->where('id',$request->team_id)->first();

        if ($team) {
            $request->user()->switchTeam($team);
            return redirect(config('fortify.home'), 303);
        }
        return redirect(config('fortify.home'), 303)->with('error', 'This group was deleted.');
    }

    public function getCurrentTopics($request): array
    {
        $topics = Topic::where('team_id', $request->user()->current_team_id)
            ->with(['createdBy', 'topicConversations', 'topicConversations.seenBy' => function ($q) use ($request) {
                $q->where([
                    ['wseen', NULL],
                    ['user_id', $request->user()->id]
                ]);
            }, 'userPermissions'])
            ->orderBy('created_at', 'DESC')->limit(5)->get();
        $response = array();
        foreach ($topics as $topic) {
            array_push($response,
                array_merge(
                    $topic->toArray(),
                    ['userStatus' => $this->getUserTopicOpenedStatus($request->user()->id, $topic->id, $request->user()->current_team_id )]
                )
            );
        }
        return $response;
    }

    public function getCreatedTopic(Request $request): \Illuminate\Http\JsonResponse
    {
        $cachedTopic = json_decode(Redis::get('topic_'.$request->topicId), true);
        if ($cachedTopic) {
            $topic = array_merge($cachedTopic, ['cached' => true]);
        } else {
            $topic = Topic::where('id', $request->topicId)->with('createdBy', 'userPermissions')->first()->toArray();
        }
        return response()->json([
            'topic' => array_merge(
                $topic,
                ['userStatus' => $this->getUserTopicOpenedStatus($request->user()->id, $topic['id'], $request->user()->current_team_id )]
            ),
            'team' => Team::where('id', $request->teamId)->first()
        ], 200);
    }
}
