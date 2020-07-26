<?php

namespace App\Listeners;

use App\Events\ClubCreated;
use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use App\Jobs\CreateClubPermissions;
use App\Jobs\CreateClubSecretaryRole;
use App\Models\Club;
use Spatie\Permission\Models\Role;

class SetUpClubSecretary
{
    public function handle(ClubCreated $event): void
    {
        /** @var Club $club */
        $club = $event->club;

        CreateClubSecretaryRole::dispatchNow($club);
        CreateClubPermissions::dispatchNow($club);

        $role = Role::findByName(RolesHelper::clubSecretary($club));
        $role->givePermissionTo([
            PermissionsHelper::viewClub($club),
            PermissionsHelper::editClub($club),
            PermissionsHelper::addTeam($club),
        ]);
    }
}
