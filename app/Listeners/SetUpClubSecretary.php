<?php

namespace App\Listeners;

use App\Events\ClubCreated;
use App\Jobs\CreateClubPermissions;
use App\Jobs\CreateClubSecretaryRole;

class SetUpClubSecretary
{
    public function handle(ClubCreated $event): void
    {
        CreateClubSecretaryRole::dispatchNow($event->club);
        CreateClubPermissions::dispatchNow($event->club);
    }
}
