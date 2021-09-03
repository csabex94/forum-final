<?php

namespace App\Actions\Jetstream;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Events\AddingTeam;
use Laravel\Jetstream\Jetstream;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param mixed $user
     * @param array $input
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException|\Illuminate\Auth\Access\AuthorizationException
     */
    public function create($user, array $input)
    {

        Gate::forUser($user)->authorize('create', Jetstream::newTeamModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'teamType' => ['required', 'string'],
        ])->validateWithBag('createTeam');

        AddingTeam::dispatch($user);

//        $user->switchTeam($team = $user->ownedTeams()->create([
//            'name' => $input['name'],
//            'personal_team' => false,
//            'receiver_id' => $input['receiverId'] ?? NULL,
//            'team_type' => $input['teamType'],
//        ]));
//        return $team;
        return $user->ownedTeams()->create([
            'name' => $input['name'],
            'personal_team' => false,
            'receiver_id' => $input['receiverId'] ?? NULL,
            'team_type' => $input['teamType'],
        ]);
    }
}
