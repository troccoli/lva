<?php

namespace App\Jobs;

use App\Models\Competition;
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
        $divisionId = $this->division->getId();

        collect([
            "edit-division-$divisionId",
            "delete-division-$divisionId",
            "add-fixtures-in-division-$divisionId",
            "edit-fixtures-in-division-$divisionId",
            "delete-fixtures-in-division-$divisionId",
            "view-fixtures-in-division-$divisionId",
        ])->each(function (string $permission): void {
            Permission::create(['name' => $permission]);
        });
    }
}
