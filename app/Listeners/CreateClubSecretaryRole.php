<?php

namespace App\Listeners;

use App\Events\ClubCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Permission\Models\Role;

class CreateClubSecretaryRole
{
    public function handle(ClubCreated $event): void
    {
        Role::create(['name' => "Club {$event->club->getId()} Secretary"]);
    }
}
