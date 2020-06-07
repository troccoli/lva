<?php

namespace App\Policies;

use App\Helpers\RolesHelper;
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
        if ($user->hasRole(RolesHelper::seasonAdminName($competition->getSeason()))) {
            return true;
        }

        if ($user->hasRole(RolesHelper::competitionAdminName($competition))) {
            return true;
        }

        return $this->hasAnyDivisionAdminRole($user, $competition);
    }

    public function create(User $user, Competition $competition): bool
    {
        if ($user->hasRole(RolesHelper::seasonAdminName($competition->getSeason()))) {
            return true;
        }

        return $user->hasRole(RolesHelper::competitionAdminName($competition));
    }

    public function update(User $user, Division $division): bool
    {
        if ($user->hasRole(RolesHelper::seasonAdminName($division->getCompetition()->getSeason()))) {
            return true;
        }

        if ($user->hasRole(RolesHelper::competitionAdminName($division->getCompetition()))) {
            return true;
        }

        return $user->hasRole(RolesHelper::divisionAdminName($division));
    }

    public function delete(User $user, Division $division): bool
    {
        if ($user->hasRole(RolesHelper::seasonAdminName($division->getCompetition()->getSeason()))) {
            return true;
        }

        return $user->hasRole(RolesHelper::competitionAdminName($division->getCompetition()));
    }
}
