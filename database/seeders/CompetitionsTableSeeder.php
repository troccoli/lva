<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Database\Seeder;

class CompetitionsTableSeeder extends Seeder
{
    public function run(): void
    {
        Season::each(function (Season $season): void {
            Competition::factory()->for($season)->create();
        });
    }
}
