<?php

namespace App\Policies;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization, CheckRoles;

    public function viewAny(User $user, Club $club): bool
    {
        if ($user->hasRole(RolesHelper::clubSecretary($club))) {
            return true;
        }

        return $this->hasAnyTeamSecretaryRole($user, $club);
    }

    public function create(User $user, Club $club): bool
    {
        return $user->hasRole(RolesHelper::clubSecretary($club));
    }

    public function update(User $user, Team $team): bool
    {
        return $user->hasAnyRole(
            RolesHelper::clubSecretary($team->getClub()),
            RolesHelper::teamSecretary($team)
        );
    }

    public function delete(User $user, Team $team): bool
    {
        return $user->hasRole(RolesHelper::clubSecretary($team->getClub()));
    }
}
