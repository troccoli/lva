<?php

namespace App\Jobs;

use App\Helpers\PermissionsHelper;
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
            PermissionsHelper::editTeam($this->team),
            PermissionsHelper::deleteTeam($this->team),
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
