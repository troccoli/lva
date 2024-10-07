<?php

namespace App\Jobs\Permissions;

use App\Helpers\PermissionsHelper;
use App\Models\Club;
use Illuminate\Support\Collection;

class CreateClubPermissions extends CreatePermissions
{
    public function __construct(private readonly Club $club) {}

    protected function getPermissions(): Collection
    {
        return collect([
            PermissionsHelper::viewClub($this->club),
            PermissionsHelper::editClub($this->club),
            PermissionsHelper::deleteClub($this->club),
            PermissionsHelper::addTeam($this->club),
        ]);
    }
}
