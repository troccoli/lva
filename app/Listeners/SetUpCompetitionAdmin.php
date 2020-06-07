<?php

namespace App\Listeners;

use App\Events\CompetitionCreated;
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
        $competitionId = $competition->getId();

        CreateCompetitionAdminRole::dispatchNow($competition);
        CreateCompetitionPermissions::dispatchNow($competition);

        /** @var Role $competitionAdminRole */
        $competitionAdminRole = Role::findByName(RolesHelper::competitionAdminName($competition));
        $competitionAdminRole->givePermissionTo([
            "edit-competition-$competitionId",
            "add-divisions-in-competition-$competitionId",
            "view-divisions-in-competition-$competitionId",
        ]);

        /** @var Role $seasonAdminRole */
        $seasonAdminRole = Role::findByName(RolesHelper::seasonAdminName($competition->getSeason()));
        $seasonAdminRole->givePermissionTo([
            "edit-competition-$competitionId",
            "delete-competition-$competitionId",
            "add-divisions-in-competition-$competitionId",
            "view-divisions-in-competition-$competitionId",
        ]);
    }
}
