<?php

namespace App\Listeners;

use App\Events\CompetitionCreated;
use Spatie\Permission\Models\Role;

class CreateCompetitionAdminRole
{
    public function handle(CompetitionCreated $event): void
    {
        Role::create(['name' => $event->competition->getAdminRole()]);
    }
}
