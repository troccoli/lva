<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FixturesSeeder extends Seeder
{
    use SeederProgressBar;

    public function run(): void
    {
        $divisions = Division::all();

        $this->initProgressBar($divisions->count());

        /** @var Division $division */
        foreach ($divisions as $division) {
            $teams = $division->teams->keyBy((new Team)->getKeyName());

            $matchDate = Carbon::today()->setYear($division->competition->season->year);
            $matchNumber = 1;
            $this->advanceProgressBar();
            foreach ($this->buildRounds($teams) as $round) {
                foreach ($round as $match) {
                    $startTime = match (true) {
                        $matchDate->isSunday() => '11:30',
                        $matchDate->isSaturday() => '15:00',
                        default => '19:45'
                    };

                    /** @var Team $homeTeam */
                    $homeTeam = $teams->get($match[0]);
                    /** @var Team $awayTeam */
                    $awayTeam = $teams->get($match[1]);

                    Fixture::factory()
                        ->create([
                            'division_id' => $division->getKey(),
                            'match_number' => $matchNumber++,
                            'match_date' => $matchDate,
                            'start_time' => $startTime,
                            'home_team_id' => $homeTeam->getKey(),
                            'away_team_id' => $awayTeam->getKey(),
                            'venue_id' => $homeTeam->venue_id,
                        ]);
                }
                $matchDate->addDay();
            }
        }
        $this->finishProgressBar();
    }

    private function buildRounds(Collection $teams): \Schedule
    {
        $rounds = (($count = count($teams)) % 2 === 0 ? $count - 1 : $count) * 2;
        $scheduler = new \ScheduleBuilder($teams->keys()->toArray(), $rounds);

        return $scheduler->build();
    }
}
