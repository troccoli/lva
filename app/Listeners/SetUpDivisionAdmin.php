<?php

namespace App\Listeners;

use App\Events\DivisionCreated;
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
        $divisionAdminRole = Role::findByName($division->getAdminRole());
        $divisionAdminRole->givePermissionTo([
            "edit-division-$divisionId",
            "add-fixtures-in-division-$divisionId",
            "edit-fixtures-in-division-$divisionId",
            "delete-fixtures-in-division-$divisionId",
            "view-fixtures-in-division-$divisionId",
        ]);

        $competitionAdminRole = Role::findByName($division->getCompetition()->getAdminRole());
        $competitionAdminRole->givePermissionTo([
            "edit-division-$divisionId",
            "delete-division-$divisionId",
            "add-fixtures-in-division-$divisionId",
            "edit-fixtures-in-division-$divisionId",
            "delete-fixtures-in-division-$divisionId",
            "view-fixtures-in-division-$divisionId",
        ]);

        $seasonAdminRole = Role::findByName($division->getCompetition()->getSeason()->getAdminRole());
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
