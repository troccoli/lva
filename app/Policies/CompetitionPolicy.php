<?php

namespace App\Policies;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetitionPolicy
{
    use HandlesAuthorization, CheckRoles;

    public function viewAny(User $user, Season $season): bool
    {
        if ($user->hasRole(RolesHelper::seasonAdminName($season))) {
            return true;
        }

        if ($this->hasAnyCompetitionAdminRole($user, $season)) {
            return true;
        }

        return false;
    }

    public function create(User $user, Season $season): bool
    {
        return $user->hasRole(RolesHelper::seasonAdminName($season));
    }

    public function update(User $user, Competition $competition): bool
    {
        if ($user->hasRole(RolesHelper::seasonAdminName($competition->getSeason()))) {
            return true;
        }

        if ($user->hasRole(RolesHelper::competitionAdminName($competition))) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Competition $competition): bool
    {
        return $user->hasRole(RolesHelper::seasonAdminName($competition->getSeason()));
    }
}
