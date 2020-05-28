<?php

namespace App\Listeners;

use App\Events\CompetitionCreated;
use App\Jobs\CreateCompetitionAdminRole;

class SetUpCompetitionAdmin
{
    public function handle(CompetitionCreated $event): void
    {
        CreateCompetitionAdminRole::dispatchNow($event->competition);
    }
}
