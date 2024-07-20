<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Seeder;

class DivisionsTeamsSeeder extends Seeder
{
    public function run(): void
    {
        Season::query()->each(function (Season $season): void {
            /** @var Competition $competition */
            $competition = $season->competitions->first();
            $divisions = $competition->divisions;

            $teams = Team::all();

            foreach ($teams->chunk(8) as $teams) {
                /** @var Division $division */
                $division = $divisions->pop();

                foreach ($teams as $team) {
                    /** @var Team $team */
                    $team->divisions()->attach($division->getKey());
                }
            }
        });
    }
}
