<?php

namespace App\Repositories\TeamInvitation;

interface InvitesTeamMembers
{
    /**
     * Invite a new team member to the given team.
     *
     * @param mixed $user
     * @param mixed $team
     * @param String $email
     * @param String $name
     * @param String|null $role
     * @param $userPhoto
     * @return void
     */
    public function invite($user, $team, string $email, String $role = NULL, String $name, $userPhoto);
}
