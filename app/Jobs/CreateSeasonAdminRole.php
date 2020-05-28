<?php

namespace App\Jobs;

use App\Models\Season;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Role;

class CreateSeasonAdminRole
{
    use Dispatchable, Queueable;

    private Season $season;

    public function __construct(Season $season)
    {
        $this->season = $season;
    }

    public function handle(): void
    {
        Role::create(['name' => $this->season->getAdminRole()]);
    }
}
