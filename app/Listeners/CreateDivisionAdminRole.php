<?php

namespace App\Listeners;

use App\Events\DivisionCreated;
use Spatie\Permission\Models\Role;

class CreateDivisionAdminRole
{
    public function handle(DivisionCreated $event): void
    {
        Role::create(['name' => $event->division->getAdminRole()]);
    }
}
