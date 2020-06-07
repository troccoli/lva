<?php

namespace App\Policies;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClubPolicy
{
    use HandlesAuthorization, CheckRoles;

    public function viewAny(User $user): bool
    {
        return $this->hasAnyClubSecretaryRole($user);
    }

    public function create(User $user): bool
    {
        // No-one but Site Administrator can create a new season
        return false;
    }

    public function update(User $user, Club $club): bool
    {
        return $user->hasRole(RolesHelper::clubSecretaryName($club));
    }

    public function delete(User $user, Club $club): bool
    {
        // No-one but Site Administrator can create a new season
        return false;
    }
}
