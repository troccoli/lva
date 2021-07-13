<?php

namespace Tests\Integration\Api\V1;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

trait TestFixtures
{
    private $season1;
    private $season2;

    private $competition1;
    private $competition2;
    private $competition3;
    private $competition4;
    private $competition5;

    private $division1;
    private $division2;
    private $division3;
    private $division4;
    private $division5;
    private $division6;

    private $venue1;
    private $venue2;
    private $venue3;

    private $club1;
    private $club2;
    private $club3;
    private $club4;

    private $team1;
    private $team2;
    private $team3;
    private $team4;
    private $team5;
    private $team6;
    private $team7;

    private function setUpFixtures(): void
    {
        $this->season1 = Season::factory()->create(['year' => 2000]);
        $this->season2 = Season::factory()->create(['year' => 2001]);

        $this->competition1 = Competition::factory()->for($this->season1)->create();
        $this->competition2 = Competition::factory()->for($this->season1)->create();
        $this->competition3 = Competition::factory()->for($this->season2)->create();
        $this->competition4 = Competition::factory()->for($this->season2)->create();
        $this->competition5 = Competition::factory()->for($this->season2)->create();

        $this->division1 = Division::factory()->for($this->competition1)->create();
        $this->division2 = Division::factory()->for($this->competition2)->create();
        $this->division3 = Division::factory()->for($this->competition2)->create();
        $this->division4 = Division::factory()->for($this->competition3)->create();
        $this->division5 = Division::factory()->for($this->competition4)->create();
        $this->division6 = Division::factory()->for($this->competition5)->create();

        $this->venue1 = Venue::factory()->create();
        $this->venue2 = Venue::factory()->create();
        $this->venue3 = Venue::factory()->create();

        $this->club1 = Club::factory()->for($this->venue1)->create();
        $this->club2 = Club::factory()->for($this->venue2)->create();
        $this->club3 = Club::factory()->for($this->venue3)->create();
        $this->club4 = Club::factory()->withoutVenue()->create();

        $this->team1 = Team::factory()
                           ->for($this->club1)
                           ->hasAttached($this->division1)
                           ->hasAttached($this->division2)
                           ->hasAttached($this->division4)
                           ->create();
        $this->team2 = Team::factory()
                           ->for($this->club2)
                           ->hasAttached($this->division1)
                           ->hasAttached($this->division2)
                           ->hasAttached($this->division4)
                           ->create();
        $this->team3 = Team::factory()
                           ->for($this->club3)
                           ->for($this->venue3)
                           ->hasAttached($this->division1)
                           ->hasAttached($this->division3)
                           ->hasAttached($this->division4)
                           ->create();
        $this->team4 = Team::factory()
                           ->for($this->club3)
                           ->hasAttached($this->division1)
                           ->hasAttached($this->division3)
                           ->hasAttached($this->division5)
                           ->create();
        $this->team5 = Team::factory()
                           ->for($this->club1)
                           ->hasAttached($this->division2)
                           ->hasAttached($this->division5)
                           ->create();
        $this->team6 = Team::factory()
                           ->for($this->club2)
                           ->hasAttached($this->division3)
                           ->hasAttached($this->division6)
                           ->for($this->venue1)
                           ->create();
        $this->team7 = Team::factory()->for($this->club4)->hasAttached($this->division6)->for($this->venue2)->create();

        $this->createRoundRobinFixtures();
    }

    private function createRoundRobinFixtures(): void
    {
        $divisions = Division::all();

        foreach ($divisions as $division) {
            $teams = $division->getTeams()->keyBy('id');

            $matchDate = Carbon::createFromDate($division->getCompetition()->getSeason()->getYear(), 8, 16);
            $matchNumber = 1;
            foreach ($this->buildRounds($teams) as $round) {
                foreach ($round as $match) {
                    /** @var Team $homeTeam */
                    $homeTeam = $teams->get($match[0]);
                    /** @var Team $awayTeam */
                    $awayTeam = $teams->get($match[1]);

                    if (null === $homeTeam || null === $awayTeam) {
                        continue;
                    }

                    $matchTime = $this->setMatchTime($matchDate);

                    Fixture::factory()
                           ->inDivision($division)
                           ->number($matchNumber++)
                           ->on($matchDate, $matchTime)
                           ->between($homeTeam, $awayTeam)
                           ->at($homeTeam->getVenue())
                           ->create();
                }
                $matchDate->addDay();
            }
        }
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
        $scheduler->doNotShuffle();

        return $scheduler->build();
    }
}
