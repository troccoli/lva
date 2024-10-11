<?php

namespace App\Jobs\Roles;

use App\Helpers\RolesHelper;
use App\Models\Team;

class CreateTeamSecretaryRole extends CreateRole
{
    public function __construct(private readonly Team $team) {}

    protected function getRole(): string
    {
        return RolesHelper::teamSecretary($this->team);
    }
}
