<?php

namespace Tests\Integration\Api\V1;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

trait TestFixtures
{
    /** @var Season */
    private $season1;
    /** @var Season */
    private $season2;

    /** @var Competition */
    private $competition1;
    /** @var Competition */
    private $competition2;
    /** @var Competition */
    private $competition3;
    /** @var Competition */
    private $competition4;
    /** @var Competition */
    private $competition5;

    /** @var Division */
    private $division1;
    /** @var Division */
    private $division2;
    /** @var Division */
    private $division3;
    /** @var Division */
    private $division4;
    /** @var Division */
    private $division5;
    /** @var Division */
    private $division6;

    /** @var Venue */
    private $venue1;
    /** @var Venue */
    private $venue2;
    /** @var Venue */
    private $venue3;

    /** @var Club */
    private $club1;
    /** @var Club */
    private $club2;
    /** @var Club */
    private $club3;
    /** @var Club */
    private $club4;

    /** @var Team */
    private $team1;
    /** @var Team */
    private $team2;
    /** @var Team */
    private $team3;
    /** @var Team */
    private $team4;
    /** @var Team */
    private $team5;
    /** @var Team */
    private $team6;
    /** @var Team */
    private $team7;

    private function setUpFixtures(): void
    {
        $this->season1 = factory(Season::class)->create(['year' => 2000]);
        $this->season2 = factory(Season::class)->create(['year' => 2001]);

        $this->competition1 = factory(Competition::class)->create(['season_id' => $this->season1->getId()]);
        $this->competition2 = factory(Competition::class)->create(['season_id' => $this->season1->getId()]);
        $this->competition3 = factory(Competition::class)->create(['season_id' => $this->season2->getId()]);
        $this->competition4 = factory(Competition::class)->create(['season_id' => $this->season2->getId()]);
        $this->competition5 = factory(Competition::class)->create(['season_id' => $this->season2->getId()]);

        $this->division1 = factory(Division::class)->create(['competition_id' => $this->competition1->getId()]);
        $this->division2 = factory(Division::class)->create(['competition_id' => $this->competition2->getId()]);
        $this->division3 = factory(Division::class)->create(['competition_id' => $this->competition2->getId()]);
        $this->division4 = factory(Division::class)->create(['competition_id' => $this->competition3->getId()]);
        $this->division5 = factory(Division::class)->create(['competition_id' => $this->competition4->getId()]);
        $this->division6 = factory(Division::class)->create(['competition_id' => $this->competition5->getId()]);

        $this->venue1 = factory(Venue::class)->create();
        $this->venue2 = factory(Venue::class)->create();
        $this->venue3 = factory(Venue::class)->create();

        $this->club1 = aClub()->withVenue($this->venue1)->build();
        $this->club2 = aClub()->withVenue($this->venue2)->build();
        $this->club3 = aClub()->withVenue($this->venue3)->build();
        $this->club4 = aClub()->withoutVenue()->build();

        $this->team1 = aTeam()->inClub($this->club1)->inDivision($this->division1)->inDivision($this->division2)->inDivision($this->division4)->build();
        $this->team2 = aTeam()->inClub($this->club2)->inDivision($this->division1)->inDivision($this->division2)->inDivision($this->division4)->build();
        $this->team3 = aTeam()->inClub($this->club3)->withVenue($this->venue3)->inDivision($this->division1)->inDivision($this->division3)->inDivision($this->division4)->build();
        $this->team4 = aTeam()->inClub($this->club3)->inDivision($this->division1)->inDivision($this->division3)->inDivision($this->division5)->build();
        $this->team5 = aTeam()->inClub($this->club1)->inDivision($this->division2)->inDivision($this->division5)->build();
        $this->team6 = aTeam()->inClub($this->club2)->inDivision($this->division3)->inDivision($this->division6)->withVenue($this->venue1)->build();
        $this->team7 = aTeam()->inClub($this->club4)->inDivision($this->division6)->withVenue($this->venue2)->build();

        $this->createRoundRobinFixtures();
    }

    private function createRoundRobinFixtures(): void
    {
        /** @var Collection $divisions */
        $divisions = Division::all();

        /** @var Division $division */
        foreach ($divisions as $division) {
            /** @var Collection $teams */
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

                    aFixture()
                        ->inDivision($division)
                        ->number($matchNumber++)
                        ->on($matchDate, $matchTime)
                        ->between($homeTeam, $awayTeam)
                        ->at($homeTeam->getVenue())
                        ->build();
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
