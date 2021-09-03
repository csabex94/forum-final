<?php

namespace App\Http\Controllers;

use App\Events\GotNewInvitationEvent;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Jetstream;
use App\Actions\Jetstream\InviteTeamMember;

class ContactController extends Controller
{
    protected $inviteTeamMember;

    public function __construct(InviteTeamMember $inviteTeamMember) {
        $this->inviteTeamMember = $inviteTeamMember;
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $creator = app(CreatesTeams::class);

        $newTeam = $creator->create($request->user(), $request->all());
        $newTeam->pending = true;
        $newTeam->save();
        $newTeam->load('owner');

        $invitation = $this->inviteTeamMember->invite($request->user(), $newTeam, $request->email, $request->role, $request->name, $request->userPhoto);
        event(new GotNewInvitationEvent($invitation, $request->userId));

        return response()->json([
            'teams' => $request->user()->allTeams()->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations'),
            'newInvitation' => array_merge($invitation->toArray(), $this->getUserByEmail($invitation['email']), ['invitationId' => $invitation->id])
        ], 200);
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first()->toArray();
    }

    public function deleteInvitation(Request $request): \Illuminate\Http\JsonResponse
    {
        $teamInvitation = TeamInvitation::where('id', $request->invitationId)->first();
        $team = Team::where('id', $teamInvitation->team_id)->first();
        $team->delete();
        $teamInvitation->delete();

        $invitationsSent = \App\Models\TeamInvitation::where('email', '<>', $request->user()->email)->with(['team' => function($q) use ($request) {
            $q->where([
                ['user_id', $request->user()->id],
                ['team_type', 'private-message']
            ])->get();
        }])->get();
        $result = array();
        foreach ($invitationsSent->load('team') as $invitation) {
            if ($invitation->team->team_type === 'private-message' && $invitation->team->user_id === $request->user()->id) {
                array_push($result, array_merge($this->getUserByEmail($invitation->email)->toArray(), ['invitationId' => $invitation->id]));
            }
        }
        return response()->json([
            'invitationsSent' => $result,
        ], 200);
    }

    public function findContacts(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = $request->q;
        if (strlen((string)$q) < 1) {
            return response()->json([], 200);
        }
        $users = User::where([
            ['name', 'LIKE', "%$q%"],
            ['admin', 0],
            ['id', '<>', $request->user()->id]
        ])->orWhere([
            ['email', 'LIKE', "%$q%"],
            ['admin', 0],
            ['id', '<>', $request->user()->id]
        ])->get();
        $result = array();
        foreach($users as $user){
            if ($this->checkIfInvitationExists($user, $request)) {
                array_push($result, array_merge($user->toArray(), ['invited' => true]));
            } else if ($this->checkIfTeamAlreadyExists($request, $user)) {
                array_push($result, array_merge($user->toArray(), ['team_exists' => true]));
            } else {
                array_push($result, $user);
            }
        }
        return response()->json($result, 200);
    }

    public function checkIfInvitationExists($user, $request): bool
    {
        $existingInvitation = TeamInvitation::where([
            ['email', $user->email],
            ['name', $user->name],
            ['user_id', $request->user()->id]
        ])->first();

        if ($existingInvitation) {
            return true;
        } else {
            return false;
        }
    }

    public function checkIfTeamAlreadyExists($request, $user): ?bool
    {
        $response = NULL;
        $existingInvitation = TeamInvitation::where([
            ['email', $request->user()->email],
            ['name', $request->user()->name],
            ['user_id', $user->id]
        ])->first();
        if ($existingInvitation) {
            $existingTeam = Team::where('id', $existingInvitation->team_id)->first();
            if ($existingTeam) {
                $response = true;
            } else {
                $response = false;
            }
        }
        return $response;
    }
}
