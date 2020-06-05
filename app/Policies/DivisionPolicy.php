<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DivisionPolicy
{
    use HandlesAuthorization, CheckRoles;

    public function viewAny(User $user, Competition $competition): bool
    {
        if ($user->hasRole($competition->getSeason()->getAdminRole())) {
            return true;
        }

        if ($user->hasRole($competition->getAdminRole())) {
            return true;
        }

        return $this->hasAnyDivisionAdminRole($user, $competition);
    }

    public function create(User $user, Competition $competition): bool
    {
        if ($user->hasRole($competition->getSeason()->getAdminRole())) {
            return true;
        }

        return $user->hasRole($competition->getAdminRole());
    }

    public function update(User $user, Division $division): bool
    {
        if ($user->hasRole($division->getCompetition()->getSeason()->getAdminRole())) {
            return true;
        }

        if ($user->hasRole($division->getCompetition()->getAdminRole())) {
            return true;
        }

        return $user->hasRole($division->getAdminRole());
    }

    public function delete(User $user, Division $division): bool
    {
        if ($user->hasRole($division->getCompetition()->getSeason()->getAdminRole())) {
            return true;
        }

        return $user->hasRole($division->getCompetition()->getAdminRole());
    }
}
