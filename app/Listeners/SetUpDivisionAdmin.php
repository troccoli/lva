<?php

namespace App\Listeners;

use App\Events\DivisionCreated;
use App\Jobs\CreateDivisionAdminRole;

class SetUpDivisionAdmin
{
    public function handle(DivisionCreated $event): void
    {
        CreateDivisionAdminRole::dispatchNow($event->division);
    }
}
