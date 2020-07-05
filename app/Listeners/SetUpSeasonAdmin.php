<?php

namespace App\Listeners;

use App\Events\SeasonCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Jobs\CreateSeasonAdminRole;
use App\Jobs\CreateSeasonPermissions;
use App\Models\Season;
use Spatie\Permission\Models\Role;

class SetUpSeasonAdmin
{
    public function handle(SeasonCreated $event): void
    {
        /** @var Season $season */
        $season = $event->season;

        CreateSeasonAdminRole::dispatchNow($season);
        CreateSeasonPermissions::dispatchNow($season);

        /** @var Role $role */
        $role = Role::findByName(RolesHelper::seasonAdminName($season));
        $role->givePermissionTo([
            PermissionsHelper::viewSeason($season),
            PermissionsHelper::editSeason($season),
            PermissionsHelper::addCompetition($season),
            PermissionsHelper::viewCompetitions($season),
        ]);
    }
}
