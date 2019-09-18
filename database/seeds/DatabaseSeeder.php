<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);

        $this->call(SeasonsTableSeeder::class);
        $this->call(CompetitionsTableSeeder::class);
        $this->call(DivisionsTableSeeder::class);

        $this->call(VenuesTableSeeder::class);

        $this->call(ClubsTableSeeder::class);
        $this->call(TeamsTableSeeder::class);

        $this->call(DivisionsTeamsTableSeeder::class);

        $this->call(FixturesTableSeeder::class);
    }
}
