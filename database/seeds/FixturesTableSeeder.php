<?php

use App\Models\Division;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Laravel\RoundRobin\RoundRobin;

class FixturesTableSeeder extends Seeder
{
    public function run(): void
    {
        Division::all()
            ->each(function (Division $division) {
                $scheduler = new RoundRobin($division->getTeams()->pluck('id')->toArray());
                $scheduler->doubleRoundRobin();

                $matchDate = Carbon::today();
                $matchNumber = 1;
                foreach ($scheduler->build() as $schedule) {
                    if ($matchDate->isSunday()) {
                        $matchDate->setTime(11, 30);
                    } elseif ($matchDate->isSaturday()) {
                        $matchDate->setTime(15, 00);
                    } else {
                        $matchDate->setTime(19, 45);
                    }

                    /** @var Team $homeTeam */
                    $homeTeam = Team::find($schedule[0])->first();
                    /** @var Team $awayTeam */
                    $awayTeam = Team::find($schedule[1])->first();

                    aFixture()
                        ->inDivision($division)
                        ->number($matchNumber++)
                        ->on($matchDate, $matchDate)
                        ->between($homeTeam, $awayTeam)
                        ->at($homeTeam->getVenue())
                        ->build();

                    $matchDate->addDay();
                }
            });
    }
}
