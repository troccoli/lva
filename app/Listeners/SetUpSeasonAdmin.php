<?php

namespace App\Listeners;

use App\Events\SeasonCreated;
use App\Jobs\CreateSeasonAdminRole;

class SetUpSeasonAdmin
{
    public function handle(SeasonCreated $event): void
    {
        CreateSeasonAdminRole::dispatchNow($event->season);
    }
}
