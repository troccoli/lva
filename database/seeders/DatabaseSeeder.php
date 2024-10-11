<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SeasonsSeeder::class);
        $this->call(CompetitionsSeeder::class);
        $this->call(DivisionsSeeder::class);

        $this->call(VenuesSeeder::class);

        $this->call(ClubsSeeder::class);
        $this->call(TeamsSeeder::class);

        $this->call(DivisionsTeamsSeeder::class);

        $this->call(FixturesSeeder::class);

        $this->call(UsersSeeder::class);
    }
}
