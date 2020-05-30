<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetitionPolicy
{
    use HandlesAuthorization, CheckRoles;

    public function viewAny(User $user, Season $season): bool
    {
        if ($user->hasRole($season->getAdminRole())) {
            return true;
        }

        if ($this->hasAnyCompetitionAdminRole($user, $season)) {
            return true;
        }

        return false;
    }

    public function create(User $user, Season $season): bool
    {
        return $user->hasRole($season->getAdminRole());
    }

    public function update(User $user, Competition $competition): bool
    {
        if ($user->hasRole($competition->getSeason()->getAdminRole())) {
            return true;
        }

        if ($user->hasRole($competition->getAdminRole())) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Competition $competition): bool
    {
        return $user->hasRole($competition->getSeason()->getAdminRole());
    }
}
