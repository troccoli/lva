<?php

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Database\Seeder;

class CompetitionsTableSeeder extends Seeder
{
    public function run(): void
    {
        $season = factory(Season::class)->create();

        factory(Competition::class)->times(5)->create(['season_id' => $season->id]);
    }
}
