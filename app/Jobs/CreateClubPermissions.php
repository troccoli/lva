<?php

namespace App\Jobs;

use App\Models\Club;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Permission;

class CreateClubPermissions
{
    use Dispatchable, Queueable;

    private Club $club;

    public function __construct(Club $club)
    {
        $this->club = $club;
    }

    public function handle()
    {
        $clubId = $this->club->getId();

        collect([
            "edit-club-$clubId",
            "delete-club-$clubId",
            "add-teams-in-club-$clubId",
            "view-teams-in-club-$clubId",
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
