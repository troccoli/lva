<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
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

        Role::create(['name' => 'Site Administrator']);
        Permission::create(['name' => 'view-seasons']);

        $this->siteAdmin = factory(User::class)->create()->assignRole('Site Administrator');
    }
}
