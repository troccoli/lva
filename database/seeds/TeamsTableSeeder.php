<?php

use App\Models\Club;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    public function run(): void
    {
        Club::each(function (Club $club): void {
            factory(Team::class)->times(2)->create(['club_id' => $club->getId()]);
        });
    }
}