<?php

namespace App\Jobs;

use App\Models\Season;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Permission;

class CreateSeasonPermissions
{
    use Dispatchable, Queueable;

    private Season $season;

    public function __construct(Season $season)
    {
        $this->season = $season;
    }

    public function handle()
    {
        $seasonId = $this->season->getId();

        collect([
            "edit-season-$seasonId",
            "delete-season-$seasonId",
            "add-competitions-in-season-$seasonId",
            "view-competitions-in-season-$seasonId",
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
