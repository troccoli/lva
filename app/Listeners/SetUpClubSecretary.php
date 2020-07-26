<?php

namespace App\Listeners;

use App\Events\ClubCreated;
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
        $clubId = $club->getId();

        CreateClubSecretaryRole::dispatchNow($club);
        CreateClubPermissions::dispatchNow($club);

        $role = Role::findByName(RolesHelper::clubSecretary($club));
        $role->givePermissionTo([
            "edit-club-$clubId",
            "add-teams-in-club-$clubId",
            "view-teams-in-club-$clubId",
        ]);
    }
}
