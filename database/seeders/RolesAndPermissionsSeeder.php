<?php

namespace Database\Seeders;

use App\Helpers\PermissionsHelper;
use App\Helpers\RolesHelper;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => PermissionsHelper::addSeason()]);
        Permission::create(['name' => PermissionsHelper::addClub()]);
        Role::create(['name' => RolesHelper::SITE_ADMIN]);
        Role::create(['name' => RolesHelper::REF_ADMIN]);
    }
}
