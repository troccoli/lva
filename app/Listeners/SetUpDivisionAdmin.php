<?php

namespace App\Listeners;

use App\Events\DivisionCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Jobs\CreateDivisionAdminRole;
use App\Jobs\CreateDivisionPermissions;
use App\Models\Division;
use Spatie\Permission\Models\Role;

class SetUpDivisionAdmin
{
    public function handle(DivisionCreated $event): void
    {
        /** @var Division $division */
        $division = $event->division;

        CreateDivisionAdminRole::dispatchNow($division);
        CreateDivisionPermissions::dispatchNow($division);

        /** @var Role $divisionAdminRole */
        $divisionAdminRole = Role::findByName(RolesHelper::divisionAdmin($division));
        $divisionAdminRole->givePermissionTo([
            PermissionsHelper::viewDivision($division),
            PermissionsHelper::editDivision($division),
            PermissionsHelper::addFixtures($division),
            PermissionsHelper::editFixtures($division),
            PermissionsHelper::deleteFixtures($division),
            PermissionsHelper::viewFixtures($division),
        ]);

        $competitionAdminRole = Role::findByName(RolesHelper::competitionAdmin($division->getCompetition()));
        $competitionAdminRole->givePermissionTo([
            PermissionsHelper::viewDivision($division),
            PermissionsHelper::editDivision($division),
            PermissionsHelper::deleteDivision($division),
            PermissionsHelper::addFixtures($division),
            PermissionsHelper::editFixtures($division),
            PermissionsHelper::deleteFixtures($division),
            PermissionsHelper::viewFixtures($division),
        ]);

        $seasonAdminRole = Role::findByName(RolesHelper::seasonAdmin($division->getCompetition()->getSeason()));
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
