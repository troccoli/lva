<?php

namespace App\Jobs\Permissions;

use App\Helpers\PermissionsHelper;
use App\Models\Division;
use Illuminate\Support\Collection;

class CreateDivisionPermissions extends CreatePermissions
{
    public function __construct(private readonly Division $division) {}

    protected function getPermissions(): Collection
    {
        return collect([
            PermissionsHelper::viewDivision($this->division),
            PermissionsHelper::editDivision($this->division),
            PermissionsHelper::deleteDivision($this->division),
            PermissionsHelper::addFixtures($this->division),
            PermissionsHelper::editFixtures($this->division),
            PermissionsHelper::deleteFixtures($this->division),
            PermissionsHelper::viewFixtures($this->division),
        ]);
    }
}
