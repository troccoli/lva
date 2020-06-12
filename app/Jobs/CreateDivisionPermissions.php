<?php

namespace App\Jobs;

use App\Helpers\PermissionsHelper;
use App\Models\Division;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Permission;

class CreateDivisionPermissions
{
    use Dispatchable, Queueable;

    private Division $division;

    public function __construct(Division $division)
    {
        $this->division = $division;
    }

    public function handle()
    {
        collect([
            PermissionsHelper::editDivision($this->division),
            PermissionsHelper::deleteDivision($this->division),
            PermissionsHelper::addFixtures($this->division),
            PermissionsHelper::editFixtures($this->division),
            PermissionsHelper::deleteFixtures($this->division),
            PermissionsHelper::viewFixtures($this->division),
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
