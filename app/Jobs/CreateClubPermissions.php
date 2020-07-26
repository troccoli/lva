<?php

namespace App\Jobs;

use App\Helpers\PermissionsHelper;
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
        collect([
            PermissionsHelper::viewClub($this->club),
            PermissionsHelper::editClub($this->club),
            PermissionsHelper::deleteClub($this->club),
            PermissionsHelper::addTeam($this->club),
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
