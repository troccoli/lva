<?php

namespace App\Listeners;

use App\Events\TeamCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Jobs\Permissions\CreateTeamPermissions;
use App\Jobs\Roles\CreateTeamSecretaryRole;
use Spatie\Permission\Models\Role;

class SetUpTeamSecretary
{
    public function handle(TeamCreated $event): void
    {
        $team = $event->team;

        CreateTeamSecretaryRole::dispatchSync($team);
        CreateTeamPermissions::dispatchSync($team);

        $teamSecretaryRole = Role::findByName(RolesHelper::teamSecretary($team));
        $teamSecretaryRole->givePermissionTo([
            PermissionsHelper::viewTeam($team),
            PermissionsHelper::editTeam($team),
            PermissionsHelper::viewClub($team->club),
        ]);

        $clubSecretaryRole = Role::findByName(RolesHelper::clubSecretary($team->club));
        $clubSecretaryRole->givePermissionTo([
            PermissionsHelper::viewTeam($team),
            PermissionsHelper::editTeam($team),
            PermissionsHelper::deleteTeam($team),
        ]);
    }
}
