<?php

namespace Tests;

use App\Helpers\RolesHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication,
        RefreshDatabase;

    protected User $siteAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => RolesHelper::SITE_ADMIN]);
        Permission::create(['name' => 'view-seasons']);

        $this->siteAdmin = factory(User::class)->create()->assignRole(RolesHelper::SITE_ADMIN);
    }

    protected function keyArrayBy(array $items, string $key): array
    {
        return Collection::make($items)
            ->keyBy($key)
            ->toArray();
    }

    protected function userWithRole(string $role): User
    {
        return factory(User::class)->create()->assignRole($role);
    }
}
