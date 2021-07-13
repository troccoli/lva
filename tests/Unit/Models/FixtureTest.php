<?php

namespace Tests\Unit\Models;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FixtureTest extends TestCase
{
    public function testItGetsTheId(): void
    {
        $fixture = Fixture::factory()->create();
        $this->assertEquals($fixture->id, $fixture->getId());
    }

    public function testItGetsTheMatchNumber(): void
    {
        $matchNumber = floor(mt_rand(1, 200));
        $fixture = Fixture::factory()->number($matchNumber)->create();

        $this->assertEquals($matchNumber, $fixture->getMatchNumber());
    }

    public function testItGetsTheDivision(): void
    {
        /** @var Division $division */
        $division = Division::factory()->create();
        $fixture = Fixture::factory()->inDivision($division)->create();

        $this->assertEquals($division->getId(), $fixture->getDivision()->getId());
    }

    public function testItsGetsTheCompetition(): void
    {
        $competition = Competition::factory()->create();
        /** @var Division $division */
        $division = Division::factory()->create(['competition_id' => $competition->getId()]);
        $fixture = Fixture::factory()->inDivision($division)->create();

        $this->assertEquals($competition->getId(), $fixture->getCompetition()->getId());
    }

    public function testItGetsTheSeason(): void
    {
        $season = Season::factory()->create();
        $competition = Competition::factory()->create(['season_id' => $season->getId()]);
        /** @var Division $division */
        $division = Division::factory()->create(['competition_id' => $competition->getId()]);
        $fixture = Fixture::factory()->inDivision($division)->create();

        $this->assertEquals($season->getId(), $fixture->getSeason()->getId());
    }

    public function testItGetsTheHomeAndAwayTeams(): void
    {
        /** @var Team $homeTeam */
        $homeTeam = Team::factory()->create();
        /** @var Team $awayTeam */
        $awayTeam = Team::factory()->create();
        $fixture = Fixture::factory()->between($homeTeam, $awayTeam)->create();

        $this->assertEquals($homeTeam->getId(), $fixture->getHomeTeam()->getId());
        $this->assertEquals($awayTeam->getId(), $fixture->getAwayTeam()->getId());
    }

    public function testItGetsTheMatchDateAndTime(): void
    {
        $matchDate = Carbon::parse('2019-05-13');
        $matchTime = Carbon::parse('15:00');
        $fixture = Fixture::factory()->on($matchDate, $matchTime)->create();

        $this->assertEquals('2019-05-13', $fixture->getMatchDate()->toDateString());
        $this->assertEquals('15:00:00', $fixture->getMatchTime()->toTimeString());
    }

    public function testItGetsTheVenue(): void
    {
        /** @var Venue $venue */
        $venue = Venue::factory()->create();
        $fixture = Fixture::factory()->at($venue)->create();

        $this->assertEquals($venue->getId(), $fixture->getVenue()->getId());
    }

    protected function setUp(): void
    {
        parent::setUp();

        // No need to create roles every time we create a model
        Event::fake();
    }
}
