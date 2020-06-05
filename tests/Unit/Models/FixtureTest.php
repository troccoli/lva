<?php

namespace Tests\Unit\Models;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Venue;
use Carbon\Carbon;
use Tests\TestCase;

class FixtureTest extends TestCase
{
    public function testItGetsTheId(): void
    {
        $fixture = aFixture()->build();
        $this->assertEquals($fixture->id, $fixture->getId());
    }

    public function testItGetsTheMatchNumber(): void
    {
        $matchNumber = floor(mt_rand(1, 200));
        $fixture = aFixture()->number($matchNumber)->build();

        $this->assertEquals($matchNumber, $fixture->getMatchNumber());
    }

    public function testItGetsTheDivision(): void
    {
        $division = factory(Division::class)->create();
        $fixture = aFixture()->inDivision($division)->build();

        $this->assertEquals($division->getId(), $fixture->getDivision()->getId());
    }

    public function testItsGetsTheCompetition(): void
    {
        $competition = factory(Competition::class)->create();
        $division = factory(Division::class)->create(['competition_id' => $competition->getId()]);
        $fixture = aFixture()->inDivision($division)->build();

        $this->assertEquals($competition->getId(), $fixture->getCompetition()->getId());
    }

    public function testItGetsTheSeason(): void
    {
        $season = factory(Season::class)->create();
        $competition = factory(Competition::class)->create(['season_id' => $season->getId()]);
        $division = factory(Division::class)->create(['competition_id' => $competition->getId()]);
        $fixture = aFixture()->inDivision($division)->build();

        $this->assertEquals($season->getId(), $fixture->getSeason()->getId());
    }

    public function testItGetsTheHomeAndAwayTeams(): void
    {
        $homeTeam = aTeam()->build();
        $awayTeam = aTeam()->build();
        $fixture = aFixture()->between($homeTeam, $awayTeam)->build();

        $this->assertEquals($homeTeam->getId(), $fixture->getHomeTeam()->getId());
        $this->assertEquals($awayTeam->getId(), $fixture->getAwayTeam()->getId());
    }

    public function testItGetsTheMatchDateAndTime(): void
    {
        $matchDate = Carbon::parse('2019-05-13');
        $matchTime = Carbon::parse('15:00');
        $fixture = aFixture()->on($matchDate, $matchTime)->build();

        $this->assertEquals('2019-05-13', $fixture->getMatchDate()->toDateString());
        $this->assertEquals('15:00:00', $fixture->getMatchTime()->toTimeString());
    }

    public function testItGetsTheVenue(): void
    {
        $venue = factory(Venue::class)->create();
        $fixture = aFixture()->at($venue)->build();

        $this->assertEquals($venue->getId(), $fixture->getVenue()->getId());
    }
}
