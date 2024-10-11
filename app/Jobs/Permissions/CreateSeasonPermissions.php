<?php

namespace App\Jobs\Permissions;

use App\Helpers\PermissionsHelper;
use App\Models\Season;
use Illuminate\Support\Collection;

class CreateSeasonPermissions extends CreatePermissions
{
    public function __construct(private readonly Season $season) {}

    protected function getPermissions(): Collection
    {
        return collect([
            PermissionsHelper::viewSeason($this->season),
            PermissionsHelper::editSeason($this->season),
            PermissionsHelper::deleteSeason($this->season),
            PermissionsHelper::addCompetition($this->season),
        ]);
    }
}
