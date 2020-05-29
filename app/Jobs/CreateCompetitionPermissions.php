<?php

namespace App\Jobs;

use App\Models\Competition;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Permission;

class CreateCompetitionPermissions
{
    use Dispatchable, Queueable;

    private Competition $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }

    public function handle()
    {
        $competitionId = $this->competition->getId();

        collect([
            "edit-competition-$competitionId",
            "delete-competition-$competitionId",
            "add-divisions-in-competition-$competitionId",
            "view-divisions-in-competition-$competitionId",
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
