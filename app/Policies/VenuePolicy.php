<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Auth\Access\HandlesAuthorization;

class VenuePolicy
{
    use HandlesAuthorization, CheckRoles;

    public function viewAny(User $user): bool
    {
        // No-one but Site Administrator can create a new season
        return false;
    }

    public function create(User $user): bool
    {
        // No-one but Site Administrator can create a new season
        return false;
    }

    public function update(User $user, Venue $venue): bool
    {
        // No-one but Site Administrator can create a new season
        return false;
    }

    public function delete(User $user, Venue $venue): bool
    {
        // No-one but Site Administrator can create a new season
        return false;
    }
}
