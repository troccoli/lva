<?php

namespace App\Jobs;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Role;

class CreateCompetitionAdminRole
{
    use Dispatchable, Queueable;

    private Competition $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function handle(): void
    {
        Role::create(['name' => RolesHelper::competitionAdminName($this->competition)]);
    }
}
