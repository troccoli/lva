<?php

namespace App\Jobs\Permissions;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

abstract class CreatePermissions
{
    use Dispatchable;

    abstract protected function getPermissions(): Collection;

    public function handle(): void
    {
        $this->getPermissions()
            ->each(function (string $permission): void {
                Permission::create(['name' => $permission]);
            });
    }
}
