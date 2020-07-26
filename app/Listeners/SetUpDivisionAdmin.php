<?php

namespace App\Listeners;

use App\Events\DivisionCreated;
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
        $divisionId = $division->getId();

        CreateDivisionAdminRole::dispatchNow($division);
        CreateDivisionPermissions::dispatchNow($division);

        /** @var Role $divisionAdminRole */
        $divisionAdminRole = Role::findByName(RolesHelper::divisionAdmin($division));
        $divisionAdminRole->givePermissionTo([
            "edit-division-$divisionId",
            "add-fixtures-in-division-$divisionId",
            "edit-fixtures-in-division-$divisionId",
            "delete-fixtures-in-division-$divisionId",
            "view-fixtures-in-division-$divisionId",
        ]);

        $competitionAdminRole = Role::findByName(RolesHelper::competitionAdmin($division->getCompetition()));
        $competitionAdminRole->givePermissionTo([
            "edit-division-$divisionId",
            "delete-division-$divisionId",
            "add-fixtures-in-division-$divisionId",
            "edit-fixtures-in-division-$divisionId",
            "delete-fixtures-in-division-$divisionId",
            "view-fixtures-in-division-$divisionId",
        ]);

        $seasonAdminRole = Role::findByName(RolesHelper::seasonAdmin($division->getCompetition()->getSeason()));
        $seasonAdminRole->givePermissionTo([
            "edit-division-$divisionId",
            "delete-division-$divisionId",
            "add-fixtures-in-division-$divisionId",
            "edit-fixtures-in-division-$divisionId",
            "delete-fixtures-in-division-$divisionId",
            "view-fixtures-in-division-$divisionId",
        ]);
    }
}
