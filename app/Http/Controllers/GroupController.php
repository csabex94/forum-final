<?php

namespace App\Http\Controllers;

use App\Actions\Jetstream\InviteTeamMember;
use App\Events\GotNewInvitationEvent;
use App\Events\GroupDeletedEvent;
use App\Events\InviteWasAcceptedEvent;
use App\Models\File;
use App\Models\PrivateMessage;
use App\Models\PrivateRead;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamUser;
use App\Models\Topic;
use App\Models\TopicConversation;
use App\Models\TopicFile;
use App\Models\TopicOpened;
use App\Models\TopicPermission;
use App\Models\TopicRead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Jetstream\Actions\ValidateTeamDeletion;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\RemovesTeamMembers;
use Laravel\Jetstream\Jetstream;
use Spatie\Activitylog\Models\Activity;

class GroupController extends Controller {

    protected $inviteTeamMember;

    public function __construct(InviteTeamMember $inviteTeamMember) {
        $this->inviteTeamMember = $inviteTeamMember;
    }

    public function acceptGroupInvitation(Request $request): \Illuminate\Http\JsonResponse
    {
        $validData = $request->validate([
            'invitationId' => 'required|integer'
        ]);
        $invitation = TeamInvitation::where('id', $validData['invitationId'])->first();
        if ($invitation) {
            $newGroupId = $invitation->team_id;
            app(AddsTeamMembers::class)->add(
                $invitation->team->owner,
                $invitation->team,
                $invitation->email,
                $invitation->role
            );

            $newGroup = Team::where('id', $newGroupId)->with('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations')->first();
            $newGroup->pending = 0;
            $newGroup->save();
            $invitation->delete();

            event(new InviteWasAcceptedEvent(
                $request->user()->name." accepted an invitation",
                $invitation->user_id,
                $request->user()->current_team_id,
                $newGroup->team_type
            ));
            return response()->json([
                'invitationGot' => TeamInvitation::where('email', $request->user()->email)->with(['team','team.owner'])->get(),
                'newGroup' => $newGroup
            ], 200);
        } else {
            return response()->json([
                'error' => 'This invitation was deleted.',
                'invitationId' => $validData['invitationId']
            ], 200);
        }
    }

    public function deleteGroup(Request $request): \Illuminate\Http\JsonResponse {
        $team = Jetstream::newTeamModel()->findOrFail($request->teamId);
        Topic::where('team_id', $team->id)->delete();
        TopicConversation::where('team_id', $team->id)->delete();

        app(ValidateTeamDeletion::class)->validate($request->user(), $team);

        $user = $request->user();
        activity()
            ->causedBy($user)
            ->performedOn($team)
            ->tap(function(Activity $activity) use ($user, $team) {
                $activity->subject_id = $team->id;
                $activity->causer_id = $user->id;
                $activity->log_name = 'deleted';
                $activity->description = 'Deleted Team #'. $team->id . ' - '. $team->name;
            })
            ->log('deleted');

        $deleter = app(DeletesTeams::class);

        $deleter->delete($team);

        return response()->json(
            $request->user()->allTeams()->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations'),
            200
        );
    }

    public function addGroupMember(Request $request): \Illuminate\Http\JsonResponse {
        $invitation = $this->inviteTeamMember->invite(
            $request->user(),
            $request->user()->currentTeam,
            $request->email,
            'editor',
            $request->name,
            $request->userPhoto
        );

        $userId = $this->getUserByEmail($request->email)->id;
        event(new GotNewInvitationEvent($invitation, $userId));

        $groupInvitations = TeamInvitation::where([
            ['team_id', $request->user()->current_team_id]
        ])->with('team')->get();

        return response()->json([
            'groupInvitation' => $groupInvitations
        ], 200);
    }

    public function getUserByEmail(String $email) {
        return User::where('email', $email)->first();
    }

    public function leaveConversation(Request $request): RedirectResponse
    {
        $team = Team::where('id', $request->user()->current_team_id)->with('users')->first();
        $teamUser = TeamUser::where('user_id', $request->userId)->first();
        if ($teamUser) {
            $teamUser->delete();
        }
        if ($team->is_deleted && $team->user_id_deleted) {
            $privateMessages = PrivateMessage::where('team_id', $team->id)->get();
            foreach ($privateMessages as $privateMessage) {
                PrivateRead::where('private_message_id', $privateMessage->id)->delete();
                File::where([
                    ['private_message_id', $privateMessage->id],
                    ['team_id', $team->id]
                ])->delete();
                $privateMessage->delete();
            }
            $team->delete();
        } else {
            $team->is_deleted = true;
            $team->user_id_deleted = $request->user()->id;
            $team->save();
        }
        $user = User::where('id', $request->user()->id)->first();
        $user->current_team_id = NULL;
        $user->save();
        return Redirect::route('home');
    }

    public function leaveGroup(Request $request): RedirectResponse
    {
        $teamUser = TeamUser::where('user_id', $request->user()->id)->first();
        if ($teamUser) {
            $teamUser->delete();
        }
        $user = User::where('id', $request->user()->id)->first();
        $userTopics = Topic::where('user_id', $user->id)->get();
        foreach($userTopics as $userTopic) {
            $topicConversations = TopicConversation::where('topic_id', $userTopic->id)->get();
            foreach ($topicConversations as $topicConversation) {
                TopicRead::where('topic_conversation_id', $topicConversation->id)->delete();
                TopicFile::where('topic_conversation_id', $topicConversation->id)->delete();
                $topicConversation->delete();
            }
            TopicOpened::where('topic_id', $userTopic->id)->delete();
            Redis::del('topic_'.$userTopic->id);
            $userTopic->delete();
        }
        $user->current_team_id = NULL;
        $user->save();
        return Redirect::route('home');
    }

    /**
     * Remove the given user from the given team.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $team = Team::where('id', $request->teamId)->first();
        if ($team) {
            $topics = Topic::where('team_id', $team->id)->get();
            foreach ($topics as $topic) {
                $topicConversations = TopicConversation::where('topic_id', $topic->id)->get();
                foreach ($topicConversations as $topicConversation) {
                    TopicRead::where('topic_conversation_id', $topicConversation->id)->delete();
                    TopicFile::where('topic_conversation_id', $topicConversation->id)->delete();
                    $topicConversation->delete();
                }
                TopicOpened::where('topic_id', $topic->id)->delete();
                TopicPermission::where('topic_id', $topic->id)->delete();
                $topic->delete();
            }
            $team->purge();
        }
        return Redirect::route('home');
    }

    public function getUserToInvite(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = $request->q;
        if (strlen($q) < 1) {
            return response()->json([], 200);
        }
        $users = User::where([
            ['name', 'LIKE', "%$q%"],
            ['admin', 0]
        ])->orWhere([
            ['email', 'LIKE', "%$q%"],
            ['admin', 0]
        ])->get();
        $response = array();
        foreach ($users as $user) {
            if (!$user->belongsToTeam($request->user()->currentTeam)) {
                if ($this->checkIfInvitationExists($user, $request->user())) {
                    array_push($response, array_merge($user->toArray(), ['invited' => true]));
                } else {
                    array_push($response, $user);
                }
            }
        }
        return response()->json($response, 200);
    }

    public function checkIfInvitationExists($user, $me): bool
    {
        $existingInvitation = TeamInvitation::where([
            ['team_id', $me->current_team_id],
            ['email', $user->email],
            ['name', $user->name]
        ])->first();

        if ($existingInvitation) {
            return true;
        } else {
            return false;
        }
    }

    public function declineInvitation(Request $request): \Illuminate\Http\JsonResponse
    {
        $validData = $request->validate([
           'invitationId' => 'required|integer'
        ]);
        $invitation = TeamInvitation::where('id', $validData['invitationId'])->first();
        if ($invitation) {
            $team = Team::where('id', $invitation->team_id)->first();
            if ($team) {
                $team->delete();
            }
            $invitation->delete();
        }
        return response()->json(TeamInvitation::where('email', $request->user()->email)->with(['team.owner'])->get(), 200);
    }
}
