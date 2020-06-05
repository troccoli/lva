<?php

namespace App\Jobs;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Role;

class CreateTeamSecretaryRole
{
    use Dispatchable, Queueable;

    private Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function handle(): void
    {
        Role::create(['name' => $this->team->getSecretaryRole()]);
    }
}
