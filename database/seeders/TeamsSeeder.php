<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamsSeeder extends Seeder
{
    public function run(): void
    {
        Club::each(function (Club $club): void {
            Team::factory()->for($club)->count(2)->create();
        });
    }
}
