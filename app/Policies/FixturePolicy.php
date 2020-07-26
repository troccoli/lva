<?php

namespace App\Policies;

use App\Helpers\RolesHelper;
use App\Models\Division;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FixturePolicy
{
    use HandlesAuthorization, CheckRoles;

    public function viewAny(User $user): bool
    {
        if ($this->hasAnySeasonAdminRole($user)) {
            return true;
        }

        if ($this->hasAnyCompetitionAdminRole($user)) {
            return true;
        }

        return $this->hasAnyDivisionAdminRole($user);
    }

    public function create(User $user, Division $division): bool
    {
        return $user->hasAnyRole(
            RolesHelper::divisionAdmin($division),
            RolesHelper::competitionAdmin($division->getCompetition()),
            RolesHelper::seasonAdmin($division->getCompetition()->getSeason())
        );
    }

    public function update(User $user, Division $division): bool
    {
        return $user->hasAnyRole(
            RolesHelper::divisionAdmin($division),
            RolesHelper::competitionAdmin($division->getCompetition()),
            RolesHelper::seasonAdmin($division->getCompetition()->getSeason())
        );
    }

    public function delete(User $user, Division $division): bool
    {
        return $user->hasAnyRole(
            RolesHelper::divisionAdmin($division),
            RolesHelper::competitionAdmin($division->getCompetition()),
            RolesHelper::seasonAdmin($division->getCompetition()->getSeason())
        );
    }
}
