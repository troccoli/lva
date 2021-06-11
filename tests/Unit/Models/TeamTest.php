<?php

namespace Tests\Unit\Models;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TeamTest extends TestCase
{
    public function testItGetsTheId(): void
    {
        $team = Team::factory()->create();
        $this->assertEquals($team->id, $team->getId());
    }

    public function testItGetsTheName(): void
    {
        $team = Team::factory()->create();
        $this->assertEquals($team->name, $team->getName());
    }

    public function testItGetsTheNameOfTheSecretaryRole(): void
    {
        /** @var Team $team */
        $team = Team::factory()->create();
        $this->assertEquals("Team {$team->getId()} Secretary", RolesHelper::teamSecretary($team));
    }

    public function testItGetsTheClub(): void
    {
        $club = Club::factory()->create();
        $team = Team::factory()->for($club)->create();
        $this->assertEquals($club->getId(), $team->getClub()->getId());
    }

    public function testItGetsTheVenue(): void
    {
        $venue = Venue::factory()->create();
        $team = Team::factory()->for($venue)->create();

        $this->assertEquals($venue->getId(), $team->getVenue()->getId());

        $club = Club::factory()->for($venue)->create();
        $team = Team::factory()->for($club)->create();

        $this->assertEquals($venue->getId(), $team->getVenue()->getId());
    }

    public function testItGetsTheVenueFromTheClub(): void
    {
        $venue = Venue::factory()->create();
        $club = Club::factory()->for($venue)->create();
        $team = Team::factory()->for($club)->create();

        $this->assertEquals($venue->getId(), $team->getVenue()->getId());
    }

    public function testItGetsTheVenueId(): void
    {
        $venue = Venue::factory()->create();
        $team = Team::factory()->for($venue)->create();

        $this->assertSame($venue->getId(), $team->getVenueId());
    }

    public function testItDoesNotGetTheVenueIdFromTheClub(): void
    {
        $venue = Venue::factory()->create();
        $club = Club::factory()->for($venue)->create();
        $team = Team::factory()->for($club)->create();

        $this->assertNull($team->getVenueId());
    }

    public function testItGetsTheDivisions(): void
    {
        $team = Team::factory()->create();
        $divisions = collect(
            [
                Division::factory()->create(),
                Division::factory()->create(),
                Division::factory()->create(),
            ]
        )->each(
            function (Division $division) use ($team): void {
                $division->teams()->attach($team);
            }
        );

        Division::factory()->create();
        Division::factory()->create();
        Division::factory()->create();
        Division::factory()->create();

        $teamDivisions = $team->getDivisions();

        $this->assertCount(3, $teamDivisions);
        $divisions->each(
            function (Division $division) use ($teamDivisions): void {
                $this->assertContains($division->getId(), $teamDivisions->pluck('id'));
            }
        );
    }

    public function testItGetsTheFixtures(): void
    {
        /** @var Team $team */
        $team = Team::factory()->create();
        $fixtures = collect(
            [
                Fixture::factory()->between($team, Team::factory()->create())->create(),
                Fixture::factory()->between($team, Team::factory()->create())->create(),
                Fixture::factory()->between($team, Team::factory()->create())->create(),
                Fixture::factory()->between($team, Team::factory()->create())->create(),
                Fixture::factory()->between($team, Team::factory()->create())->create(),
                Fixture::factory()->between(Team::factory()->create(), $team)->create(),
                Fixture::factory()->between(Team::factory()->create(), $team)->create(),
                Fixture::factory()->between(Team::factory()->create(), $team)->create(),
            ]
        );

        Fixture::factory()->create();
        Fixture::factory()->create();
        Fixture::factory()->create();

        $teamFixtures = $team->getFixtures();

        $this->assertCount(8, $teamFixtures);
        $fixtures->each(
            function (Fixture $fixture) use ($teamFixtures): void {
                $this->assertContains($fixture->getId(), $teamFixtures->pluck('id'));
            }
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        // No need to create roles every time we create a model
        Event::fake();
    }
}
