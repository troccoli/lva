<?php

namespace App\Listeners;

use App\Events\ClubCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Jobs\Permissions\CreateClubPermissions;
use App\Jobs\Roles\CreateClubSecretaryRole;
use Spatie\Permission\Models\Role;

class SetUpClubSecretary
{
    public function handle(ClubCreated $event): void
    {
        $club = $event->club;

        CreateClubSecretaryRole::dispatchSync($club);
        CreateClubPermissions::dispatchSync($club);

        $role = Role::findByName(RolesHelper::clubSecretary($club));
        $role->givePermissionTo([
            PermissionsHelper::viewClub($club),
            PermissionsHelper::editClub($club),
            PermissionsHelper::addTeam($club),
        ]);
    }
}
