<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        Permission::create(['name' => 'manage raw data']);

        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'League Admin'])
            ->givePermissionTo('manage raw data');
        Role::create(['name' => 'Division Admin']);
    }
}
