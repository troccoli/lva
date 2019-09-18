<?php

use App\Models\Division;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class FixturesTableSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = Division::all();

        ProgressBar::setFormatDefinition('normal', '[%bar%] %percent:3s%%');
        $progressBar = new ProgressBar(new ConsoleOutput());
        $progressBar->start($divisions->count());

        /** @var Division $division */
        foreach ($divisions as $division) {
            $teams = $division->getTeams()->keyBy('id');

            $matchDate = Carbon::today()->setYear($division->getCompetition()->getSeason()->getYear());
            $matchNumber = 1;
            $progressBar->advance();
            foreach ($this->buildRounds($teams) as $round) {
                foreach ($round as $match) {
                    $matchTime = $this->setMatchTime($matchDate);

                    $homeTeam = $teams->get($match[0]);
                    $awayTeam = $teams->get($match[1]);

                    aFixture()
                        ->inDivision($division)
                        ->number($matchNumber++)
                        ->on($matchDate, $matchTime)
                        ->between($homeTeam, $awayTeam)
                        ->at($homeTeam->getVenue())
                        ->build();
                }
                $matchDate->addDay();
            };
        };
        $progressBar->finish();
        echo "\n";
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

    private function buildRounds(Collection $teams): Schedule
    {
        $rounds = (($count = count($teams)) % 2 === 0 ? $count - 1 : $count) * 2;
        $scheduler = new ScheduleBuilder($teams->keys()->toArray(), $rounds);

        return $scheduler->build();
    }
}
