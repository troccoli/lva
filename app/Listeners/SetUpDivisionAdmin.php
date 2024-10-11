<?php

namespace App\Listeners;

use App\Events\DivisionCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Jobs\Permissions\CreateDivisionPermissions;
use App\Jobs\Roles\CreateDivisionAdminRole;
use Spatie\Permission\Models\Role;

class SetUpDivisionAdmin
{
    public function handle(DivisionCreated $event): void
    {
        $division = $event->division;

        CreateDivisionAdminRole::dispatchSync($division);
        CreateDivisionPermissions::dispatchSync($division);

        $divisionAdminRole = Role::findByName(RolesHelper::divisionAdmin($division));
        $divisionAdminRole->givePermissionTo([
            PermissionsHelper::viewDivision($division),
            PermissionsHelper::editDivision($division),
            PermissionsHelper::addFixtures($division),
            PermissionsHelper::editFixtures($division),
            PermissionsHelper::deleteFixtures($division),
            PermissionsHelper::viewFixtures($division),
            PermissionsHelper::viewCompetition($division->competition),
            PermissionsHelper::viewSeason($division->competition->season),
        ]);

        $competitionAdminRole = Role::findByName(RolesHelper::competitionAdmin($division->competition));
        $competitionAdminRole->givePermissionTo([
            PermissionsHelper::viewDivision($division),
            PermissionsHelper::editDivision($division),
            PermissionsHelper::deleteDivision($division),
            PermissionsHelper::addFixtures($division),
            PermissionsHelper::editFixtures($division),
            PermissionsHelper::deleteFixtures($division),
            PermissionsHelper::viewFixtures($division),
        ]);

        $seasonAdminRole = Role::findByName(RolesHelper::seasonAdmin($division->competition->season));
        $seasonAdminRole->givePermissionTo([
            PermissionsHelper::viewDivision($division),
            PermissionsHelper::editDivision($division),
            PermissionsHelper::deleteDivision($division),
            PermissionsHelper::addFixtures($division),
            PermissionsHelper::editFixtures($division),
            PermissionsHelper::deleteFixtures($division),
            PermissionsHelper::viewFixtures($division),
        ]);
    }
}
