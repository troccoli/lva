<?php

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Database\Seeder;

class CompetitionsTableSeeder extends Seeder
{
    public function run(): void
    {
        Season::each(function (Season $season): void {
            factory(Competition::class)->create(['season_id' => $season->id]);
        });
    }
}
