<?php

namespace App\Jobs\Permissions;

use App\Helpers\PermissionsHelper;
use App\Models\Competition;
use Illuminate\Support\Collection;

class CreateCompetitionPermissions extends CreatePermissions
{
    public function __construct(private readonly Competition $competition) {}

    protected function getPermissions(): Collection
    {
        return collect([
            PermissionsHelper::viewCompetition($this->competition),
            PermissionsHelper::editCompetition($this->competition),
            PermissionsHelper::deleteCompetition($this->competition),
            PermissionsHelper::addDivision($this->competition),
        ]);
    }
}
