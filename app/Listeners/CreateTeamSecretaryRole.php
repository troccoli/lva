<?php

namespace App\Listeners;

use App\Events\TeamCreated;
use Spatie\Permission\Models\Role;

class CreateTeamSecretaryRole
{
    public function handle(TeamCreated $event): void
    {
        Role::create(['name' => "Team {$event->team->getId()} Secretary"]);
    }
}
