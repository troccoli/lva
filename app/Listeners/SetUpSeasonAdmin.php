<?php

namespace App\Listeners;

use App\Events\SeasonCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Jobs\Permissions\CreateSeasonPermissions;
use App\Jobs\Roles\CreateSeasonAdminRole;
use Spatie\Permission\Models\Role;

class SetUpSeasonAdmin
{
    public function handle(SeasonCreated $event): void
    {
        $season = $event->season;

        CreateSeasonAdminRole::dispatchSync($season);
        CreateSeasonPermissions::dispatchSync($season);

        $role = Role::findByName(RolesHelper::seasonAdmin($season));
        $role->givePermissionTo([
            PermissionsHelper::viewSeason($season),
            PermissionsHelper::editSeason($season),
            PermissionsHelper::addCompetition($season),
        ]);
    }
}
