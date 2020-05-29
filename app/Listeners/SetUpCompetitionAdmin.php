<?php

namespace App\Listeners;

use App\Events\CompetitionCreated;
use App\Jobs\CreateCompetitionAdminRole;
use App\Jobs\CreateCompetitionPermissions;

class SetUpCompetitionAdmin
{
    public function handle(CompetitionCreated $event): void
    {
        CreateCompetitionAdminRole::dispatchNow($event->competition);
        CreateCompetitionPermissions::dispatchNow($event->competition);
    }
}
