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


        Permission::create(['name' => 'manage raw data']);

        Role::create(['name' => 'Site Admin']);
        Role::create(['name' => 'League Admin'])
            ->givePermissionTo('manage raw data');
        Role::create(['name' => 'Division Admin']);

        $allRolesCount = Season::count() + Competition::count() + Division::count() +
            Club::count() + Team::count();

        $this->initProgressBar($allRolesCount);
        Season::all()->each(function (Season $season) {
            Role::create(['name' => "Season {$season->getId()} Admin"]);
            $this->advanceProgressBar();
        });
        Competition::all()->each(function (Competition $competition) {
            Role::create(['name' => "Competition {$competition->getId()} Admin"]);
            $this->advanceProgressBar();
        });
        Division::all()->each(function (Division $division) {
            Role::create(['name' => "Division {$division->getId()} Admin"]);
            $this->advanceProgressBar();
        });

        Club::all()->each(function (Club $club) {
            Role::create(['name' => "Club {$club->getId()} Secretary"]);
            $this->advanceProgressBar();
        });
        Team::all()->each(function (Team $team) {
            Role::create(['name' => "Team {$team->getId()} Secretary"]);
            $this->advanceProgressBar();
        });
        $this->finishProgressBar();
    }
}
