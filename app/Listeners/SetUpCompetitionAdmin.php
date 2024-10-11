<?php

namespace App\Listeners;

use App\Events\CompetitionCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Jobs\Permissions\CreateCompetitionPermissions;
use App\Jobs\Roles\CreateCompetitionAdminRole;
use Spatie\Permission\Models\Role;

class SetUpCompetitionAdmin
{
    public function handle(CompetitionCreated $event): void
    {
        $competition = $event->competition;

        CreateCompetitionAdminRole::dispatchSync($competition);
        CreateCompetitionPermissions::dispatchSync($competition);

        $competitionAdminRole = Role::findByName(RolesHelper::competitionAdmin($competition));
        $competitionAdminRole->givePermissionTo([
            PermissionsHelper::viewCompetition($competition),
            PermissionsHelper::editCompetition($competition),
            PermissionsHelper::addDivision($competition),
            PermissionsHelper::viewSeason($competition->season),
        ]);

        /** @var Role $seasonAdminRole */
        $seasonAdminRole = Role::findByName(RolesHelper::seasonAdmin($competition->season));
        $seasonAdminRole->givePermissionTo([
            PermissionsHelper::viewCompetition($competition),
            PermissionsHelper::editCompetition($competition),
            PermissionsHelper::deleteCompetition($competition),
            PermissionsHelper::addDivision($competition),
        ]);
    }
}
