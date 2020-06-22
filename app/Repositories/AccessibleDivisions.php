<?php

namespace App\Repositories;

use App\Helpers\PermissionsHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AccessibleDivisions
{
    public function inCompetition(User $user, Competition $competition): Collection
    {
        return $competition->getDivisions()
            ->filter(function (Division $division) use ($user, $competition): bool {
                return $user->can(PermissionsHelper::viewDivisions($competition)) ||
                    $user->can(PermissionsHelper::viewFixtures($division));
            });
    }
}
