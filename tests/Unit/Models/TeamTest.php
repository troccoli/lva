<?php

namespace Tests\Unit\Models;

use App\Models\Club;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TeamTest extends TestCase
{
    public function testItGetsTheId(): void
    {
        /** @var Team $team */
        $team = aTeam()->build();
        $this->assertEquals($team->id, $team->getId());
    }

    public function testItGetsTheName(): void
    {
        /** @var Team $team */
        $team = aTeam()->build();
        $this->assertEquals($team->name, $team->getName());
    }

    public function testItGetsTheNameOfTheSecretaryRole(): void
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();
        $this->assertEquals("Team {$team->getId()} Secretary", $team->getSecretaryRole());
    }

    public function testItGetsTheClub(): void
    {
        $club = factory(Club::class)->create();
        /** @var Team $team */
        $team = aTeam()->inClub($club)->build();
        $this->assertEquals($club->getId(), $team->getClub()->getId());
    }

    public function testItGetsTheVenue(): void
    {
        $venue = factory(Venue::class)->create();
        $team = aTeam()->withVenue($venue)->build();

        $this->assertEquals($venue->getId(), $team->getVenue()->getId());

        $club = aClub()->withVenue($venue)->build();
        $team = aTeam()->inClub($club)->build();

        $this->assertEquals($venue->getId(), $team->getVenue()->getId());
    }

    public function testItGetsTheVenueFromTheClub(): void
    {
        $venue = factory(Venue::class)->create();
        $club = aClub()->withVenue($venue)->build();
        $team = aTeam()->inClub($club)->build();

        $this->assertEquals($venue->getId(), $team->getVenue()->getId());
    }

    public function testItGetsTheVenueId(): void
    {
        $venue = factory(Venue::class)->create();
        $team = aTeam()->withVenue($venue)->build();

        $this->assertSame($venue->getId(), $team->getVenueId());
    }

    public function testItDoesNotGetTheVenueIdFromTheClub(): void
    {
        $venue = factory(Venue::class)->create();
        $club = aClub()->withVenue($venue)->build();
        $team = aTeam()->inClub($club)->build();

        $this->assertNull($team->getVenueId());
    }

    public function testItGetsTheDivisions(): void
    {
        /** @var Team $team */
        $team = aTeam()->build();
        $divisions = collect([
            factory(Division::class)->create(),
            factory(Division::class)->create(),
            factory(Division::class)->create(),
        ])->each(function (Division $division) use ($team): void {
            $division->teams()->attach($team);
        });

        // Other divisions
        factory(Division::class)->create();
        factory(Division::class)->create();
        factory(Division::class)->create();
        factory(Division::class)->create();

        $teamDivisions = $team->getDivisions();

        $this->assertCount(3, $teamDivisions);
        $divisions->each(function (Division $division) use ($teamDivisions): void {
            $this->assertContains($division->getId(), $teamDivisions->pluck('id'));
        });
    }

    public function testItGetsTheFixtures(): void
    {
        $team = aTeam()->build();
        $fixtures = collect([
            aFixture()->between($team, aTeam()->build())->build(),
            aFixture()->between($team, aTeam()->build())->build(),
            aFixture()->between($team, aTeam()->build())->build(),
            aFixture()->between($team, aTeam()->build())->build(),
            aFixture()->between($team, aTeam()->build())->build(),
            aFixture()->between(aTeam()->build(), $team)->build(),
            aFixture()->between(aTeam()->build(), $team)->build(),
            aFixture()->between(aTeam()->build(), $team)->build(),
        ]);

        // Other fixtures
        aFixture()->build();
        aFixture()->build();
        aFixture()->build();

        /** @var Collection $teamFixtures */
        $teamFixtures = $team->getFixtures();

        $this->assertCount(8, $teamFixtures);
        $fixtures->each(function (Fixture $fixture) use ($teamFixtures): void {
            $this->assertContains($fixture->getId(), $teamFixtures->pluck('id'));
        });
    }
}
