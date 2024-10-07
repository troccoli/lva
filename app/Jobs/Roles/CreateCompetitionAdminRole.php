<?php

namespace App\Jobs\Roles;

use App\Helpers\RolesHelper;
use App\Models\Competition;

class CreateCompetitionAdminRole extends CreateRole
{
    public function __construct(private readonly Competition $competition) {}

    protected function getRole(): string
    {
        return RolesHelper::competitionAdmin($this->competition);
    }
}
