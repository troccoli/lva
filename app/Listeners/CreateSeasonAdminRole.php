<?php

namespace App\Listeners;

use App\Events\SeasonCreated;
use Spatie\Permission\Models\Role;

class CreateSeasonAdminRole
{
    public function handle(SeasonCreated $event): void
    {
        Role::create(['name' => $event->season->getAdminRole()]);
    }
}
