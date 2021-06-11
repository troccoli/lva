<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Fixture;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FixturesTableSeeder extends Seeder
{
    use SeederProgressBar;

    public function run(): void
    {
        $divisions = Division::all();

        $this->initProgressBar($divisions->count());

        /** @var Division $division */
        foreach ($divisions as $division) {
            $teams = $division->getTeams()->keyBy('id');

            $matchDate = Carbon::today()->setYear($division->getCompetition()->getSeason()->getYear());
            $matchNumber = 1;
            $this->advanceProgressBar();
            foreach ($this->buildRounds($teams) as $round) {
                foreach ($round as $match) {
                    $matchTime = $this->setMatchTime($matchDate);

                    $homeTeam = $teams->get($match[0]);
                    $awayTeam = $teams->get($match[1]);

                    Fixture::factory()
                        ->create([
                            'division_id' => $division->getId(),
                            'match_number' => $matchNumber++,
                            'match_date' => $matchDate,
                            'match_time' => $matchTime,
                            'home_team_id' => $homeTeam->getId(),
                            'away_team_id' => $awayTeam->getId(),
                            'venue_id' => $homeTeam->getVenue()->getId(),
                        ]);
                }
                $matchDate->addDay();
            }
        }
        $this->finishProgressBar();
    }

    private function setMatchTime(Carbon $matchDate): Carbon
    {
        if ($matchDate->isSunday()) {
            $matchDate->setTime(11, 30);
        } elseif ($matchDate->isSaturday()) {
            $matchDate->setTime(15, 00);
        } else {
            $matchDate->setTime(19, 45);
        }

        return $matchDate;
    }

    private function buildRounds(Collection $teams): \Schedule
    {
        $rounds = (($count = count($teams)) % 2 === 0 ? $count - 1 : $count) * 2;
        $scheduler = new \ScheduleBuilder($teams->keys()->toArray(), $rounds);

        return $scheduler->build();
    }
}
