<?php

namespace App\Listeners;

use App\Events\DivisionCreated;
use App\Jobs\CreateDivisionAdminRole;
use App\Jobs\CreateDivisionPermissions;

class SetUpDivisionAdmin
{
    public function handle(DivisionCreated $event): void
    {
        CreateDivisionAdminRole::dispatchNow($event->division);
        CreateDivisionPermissions::dispatchNow($event->division);
    }
}
