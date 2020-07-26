<?php

namespace App\Jobs;

use App\Helpers\PermissionsHelper;
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
        collect([
            PermissionsHelper::viewSeason($this->season),
            PermissionsHelper::editSeason($this->season),
            PermissionsHelper::deleteSeason($this->season),
            PermissionsHelper::addCompetition($this->season),
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
