<?php

namespace App\Listeners;

use App\Events\TeamCreated;
use App\Jobs\CreateTeamPermissions;
use App\Jobs\CreateTeamSecretaryRole;
use App\Models\Team;
use Spatie\Permission\Models\Role;

class SetUpTeamSecretary
{
    public function handle(TeamCreated $event): void
    {
        /** @var Team $team */
        $team = $event->team;
        $teamId = $team->getId();

        CreateTeamSecretaryRole::dispatchNow($team);
        CreateTeamPermissions::dispatchNow($team);

        /** @var Role $teamSecretaryRole */
        $teamSecretaryRole = Role::findByName($team->getSecretaryRole());
        $teamSecretaryRole->givePermissionTo([
            "edit-team-$teamId",
        ]);

        $clubSecretaryRole = Role::findByName($team->getClub()->getSecretaryRole());
        $clubSecretaryRole->givePermissionTo([
            "edit-team-$teamId",
            "delete-team-$teamId",
        ]);
    }
}
