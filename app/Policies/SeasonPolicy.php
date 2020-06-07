<?php

namespace App\Policies;

use App\Helpers\RolesHelper;
use App\Models\Season;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeasonPolicy
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

        if ($this->hasAnyDivisionAdminRole($user)) {
            return true;
        }

        if ($this->hasAnyClubSecretaryRole($user)) {
            return true;
        }

        if ($this->hasAnyTeamSecretaryRole($user)) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // No-one but Site Administrator can create a new season
        return false;
    }

    public function update(User $user, Season $season): bool
    {
        return $user->hasRole(RolesHelper::seasonAdminName($season));
    }

    public function delete(User $user, Season $season): bool
    {
        // No-one but Site Administrator can create a new season
        return false;
    }
}
