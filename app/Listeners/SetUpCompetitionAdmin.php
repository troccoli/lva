<?php

namespace App\Listeners;

use App\Events\CompetitionCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Jobs\CreateCompetitionAdminRole;
use App\Jobs\CreateCompetitionPermissions;
use App\Models\Competition;
use Spatie\Permission\Models\Role;

class SetUpCompetitionAdmin
{
    public function handle(CompetitionCreated $event): void
    {
        /** @var Competition $competition */
        $competition = $event->competition;

        CreateCompetitionAdminRole::dispatchNow($competition);
        CreateCompetitionPermissions::dispatchNow($competition);

        /** @var Role $competitionAdminRole */
        $competitionAdminRole = Role::findByName(RolesHelper::competitionAdmin($competition));
        $competitionAdminRole->givePermissionTo([
            PermissionsHelper::viewCompetition($competition),
            PermissionsHelper::editCompetition($competition),
            PermissionsHelper::addDivision($competition),
        ]);

        /** @var Role $seasonAdminRole */
        $seasonAdminRole = Role::findByName(RolesHelper::seasonAdmin($competition->getSeason()));
        $seasonAdminRole->givePermissionTo([
            PermissionsHelper::viewCompetition($competition),
            PermissionsHelper::editCompetition($competition),
            PermissionsHelper::deleteCompetition($competition),
            PermissionsHelper::addDivision($competition),
        ]);
    }
}
