<?php

namespace App\Listeners;

use App\Events\TeamCreated;
use App\Jobs\CreateTeamSecretaryRole;

class SetUpTeamSecretary
{
    public function handle(TeamCreated $event): void
    {
        CreateTeamSecretaryRole::dispatchNow($event->team);
    }
}
