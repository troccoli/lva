<?php

namespace App\Repositories;

use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AccessibleSeasons
{
    public function get(User $user): Collection
    {
        return Season::all()
            ->filter(function (Season $season) use ($user): bool {
                return $user->can(PermissionsHelper::viewSeason($season));
            });
    }
}
