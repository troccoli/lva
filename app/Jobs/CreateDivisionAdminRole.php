<?php

namespace App\Jobs;

use App\Models\Division;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Role;

class CreateDivisionAdminRole
{
    use Dispatchable, Queueable;

    private Division $division;

    public function __construct(Division $division)
    {
        $this->division = $division;
    }

    public function handle(): void
    {
        Role::create(['name' => $this->division->getAdminRole()]);
    }
}
