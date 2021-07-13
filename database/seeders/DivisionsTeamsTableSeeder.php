<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Seeder;

class DivisionsTeamsTableSeeder extends Seeder
{
    public function run(): void
    {
        Season::each(function (Season $season): void {
            /** @var Competition $competition */
            $competition = $season->getCompetitions()->first();
            $divisions = $competition->getDivisions();

            $teams = Team::all();

            foreach ($teams->chunk(8) as $teams) {
                /** @var Division $division */
                $division = $divisions->pop();

                foreach ($teams as $team) {
                    /** @var Team $team */
                    $team->divisions()->attach($division->getId());
                }
            }
        });
    }
}
