<?php

namespace App\Jobs;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Permission;

class CreateTeamPermissions
{
    use Dispatchable, Queueable;

    private Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function handle()
    {
        $teamId = $this->team->getId();

        collect([
            "edit-team-$teamId",
            "delete-team-$teamId",
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
