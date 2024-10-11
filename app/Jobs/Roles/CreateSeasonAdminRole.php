<?php

namespace App\Jobs\Roles;

use App\Helpers\RolesHelper;
use App\Models\Season;

class CreateSeasonAdminRole extends CreateRole
{
    public function __construct(private readonly Season $season) {}

    protected function getRole(): string
    {
        return RolesHelper::seasonAdmin($this->season);
    }
}
