<?php

namespace App\Jobs\Roles;

use App\Helpers\RolesHelper;
use App\Models\Club;

class CreateClubSecretaryRole extends CreateRole
{
    public function __construct(private readonly Club $club) {}

    protected function getRole(): string
    {
        return RolesHelper::clubSecretary($this->club);
    }
}
