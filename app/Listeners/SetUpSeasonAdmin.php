<?php

namespace App\Listeners;

use App\Events\SeasonCreated;
use App\Jobs\CreateSeasonAdminRole;
use App\Jobs\CreateSeasonPermissions;

class SetUpSeasonAdmin
{
    public function handle(SeasonCreated $event): void
    {
        CreateSeasonAdminRole::dispatchNow($event->season);
        CreateSeasonPermissions::dispatchNow($event->season);
    }
}
