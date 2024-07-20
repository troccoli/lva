<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Database\Seeder;

class CompetitionsSeeder extends Seeder
{
    public function run(): void
    {
        Season::query()->each(function (Season $season): void {
            Competition::factory()->for($season)->count(2)->create();
        });
    }
}
