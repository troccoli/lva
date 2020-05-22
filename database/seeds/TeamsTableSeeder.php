<?php

use App\Models\Club;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TeamsTableSeeder extends Seeder
{
    public function run(): void
    {
        Club::each(function (Club $club): void {
            factory(Team::class)->times(2)->create(['club_id' => $club->getId()]);
        });

        $this->createRoles();
    }

    private function createRoles(): void
    {
        Team::all()->each(function (Team $team) {
            Role::create(['name' => "Team {$team->getId()} Secretary"]);
        });
    }
}
