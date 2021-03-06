<?php

namespace App\Listeners;

use App\Events\TeamCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
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

        CreateTeamSecretaryRole::dispatchNow($team);
        CreateTeamPermissions::dispatchNow($team);

        /** @var Role $teamSecretaryRole */
        $teamSecretaryRole = Role::findByName(RolesHelper::teamSecretary($team));
        $teamSecretaryRole->givePermissionTo([
            PermissionsHelper::viewTeam($team),
            PermissionsHelper::editTeam($team),
            PermissionsHelper::viewClub($team->getClub()),
        ]);

        $clubSecretaryRole = Role::findByName(RolesHelper::clubSecretary($team->getClub()));
        $clubSecretaryRole->givePermissionTo([
            PermissionsHelper::viewTeam($team),
            PermissionsHelper::editTeam($team),
            PermissionsHelper::deleteTeam($team),
        ]);
    }
}
