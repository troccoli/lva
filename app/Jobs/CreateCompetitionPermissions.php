<?php

namespace App\Jobs;

use App\Helpers\PermissionsHelper;
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
        collect([
            PermissionsHelper::viewCompetition($this->competition),
            PermissionsHelper::editCompetition($this->competition),
            PermissionsHelper::deleteCompetition($this->competition),
            PermissionsHelper::addDivision($this->competition),
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
