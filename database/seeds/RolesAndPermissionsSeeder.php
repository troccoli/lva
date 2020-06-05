<?php

use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    use SeederProgressBar;

    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        Permission::create(['name' => 'view-seasons']);

        Role::create(['name' => 'Site Administrator']);
        Role::create(['name' => 'Referees Administrator']);
        Role::create(['name' => 'League Administrator'])
            ->givePermissionTo('view-seasons');
        Role::create(['name' => 'Division Administrator']);
    }
}
