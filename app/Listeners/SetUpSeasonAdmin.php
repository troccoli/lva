<?php

namespace App\Listeners;

use App\Events\SeasonCreated;
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
        $seasonId = $season->getId();

        CreateSeasonAdminRole::dispatchNow($season);
        CreateSeasonPermissions::dispatchNow($season);

        /** @var Role $role */
        $role = Role::findByName($season->getAdminRole());
        $role->givePermissionTo([
            "edit-season-$seasonId",
            "add-competitions-in-season-$seasonId",
            "view-competitions-in-season-$seasonId",
        ]);
    }
}
