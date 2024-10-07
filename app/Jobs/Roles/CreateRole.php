<?php

namespace App\Jobs\Roles;

use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Permission\Models\Role;

abstract class CreateRole
{
    use Dispatchable;

    abstract protected function getRole(): string;

    public function handle(): void
    {
        Role::create(['name' => $this->getRole()]);
    }
}
