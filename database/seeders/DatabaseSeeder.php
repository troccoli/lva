<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SeasonsTableSeeder::class);
        $this->call(CompetitionsTableSeeder::class);
        $this->call(DivisionsTableSeeder::class);

        $this->call(VenuesTableSeeder::class);

        $this->call(ClubsTableSeeder::class);
        $this->call(TeamsTableSeeder::class);

        $this->call(DivisionsTeamsTableSeeder::class);

        $this->call(FixturesTableSeeder::class);

        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersTableSeeder::class);
    }
}
