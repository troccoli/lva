<?php

namespace App\Jobs\Roles;

use App\Helpers\RolesHelper;
use App\Models\Division;

class CreateDivisionAdminRole extends CreateRole
{
    public function __construct(private readonly Division $division) {}

    protected function getRole(): string
    {
        return RolesHelper::divisionAdmin($this->division);
    }
}
