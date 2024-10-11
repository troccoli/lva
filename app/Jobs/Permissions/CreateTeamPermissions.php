<?php

namespace App\Jobs\Permissions;

use App\Helpers\PermissionsHelper;
use App\Models\Team;
use Illuminate\Support\Collection;

class CreateTeamPermissions extends CreatePermissions
{
    public function __construct(private readonly Team $team) {}

    protected function getPermissions(): Collection
    {
        return collect([
            PermissionsHelper::viewTeam($this->team),
            PermissionsHelper::editTeam($this->team),
            PermissionsHelper::deleteTeam($this->team),
        ]);
    }
}
