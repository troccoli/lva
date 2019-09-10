<?php

namespace Tests\Unit\Models;

use App\Models\Club;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ClubTest extends TestCase
{
    use RefreshDatabase;

    public function testItGetsTheId(): void
    {
        /** @var Club $club */
        $club = factory(Club::class)->create();
        $this->assertEquals($club->id, $club->getId());
    }

    public function testItGetsTheName(): void
    {
        /** @var Club $club */
        $club = factory(Club::class)->create();
        $this->assertEquals($club->name, $club->getName());
    }

    public function testItGetsTheTeams(): void
    {
        /** @var Club $club */
        $club = factory(Club::class)->create();

        $teams = collect([
            aTeam()->inClub($club)->build(),
            aTeam()->inClub($club)->build(),
            aTeam()->inClub($club)->build(),
        ]);
        aTeam()->build();
        aTeam()->build();
        aTeam()->build();
        aTeam()->build();

        $this->assertCount(3, $club->getTeams());
        $teams->each(function (Team $team) use ($club): void {
            $this->assertTrue($club->getTeams()->contains($team));
        });
    }

    public function testItGetsTheVenue(): void
    {
        $venue = factory(Venue::class)->create(['name' => 'Olympic Stadium']);
        $club = aClub()->withVenue($venue)->build();

        $this->assertEquals($venue->toArray(), $club->getVenue()->toArray());
    }

    public function testItGetsTheVenueId(): void
    {
        $venue = factory(Venue::class)->create(['name' => 'Olympic Stadium']);
        $club = aClub()->withVenue($venue)->build();

        $this->assertSame($venue->getId(), $club->getVenueId());
    }

    public function testItGetsTheFixtures(): void
    {
        $club = aClub()->build();
        $team1 = aTeam()->inClub($club)->build();
        $team2 = aTeam()->inClub($club)->build();
        $fixtures = collect([
            aFixture()->between($team1, aTeam()->build())->build(),
            aFixture()->between($team1, aTeam()->build())->build(),
            aFixture()->between($team2, aTeam()->build())->build(),
            aFixture()->between($team2, aTeam()->build())->build(),
            aFixture()->between(aTeam()->build(), $team2)->build(),
            aFixture()->between(aTeam()->build(), $team1)->build(),
            aFixture()->between(aTeam()->build(), $team2)->build(),
        ]);
        $otherFixtures = collect([
            aFixture()->build(),
            aFixture()->build(),
            aFixture()->build(),
            aFixture()->build(),
            aFixture()->build(),
        ]);

        /** @var Collection $clubFixtures */
        $clubFixtures = $club->getFixtures();

        $this->assertCount(7, $clubFixtures);
        $fixtures->each(function (Fixture $fixture) use ($clubFixtures): void {
            $this->assertContains($fixture->getId(), $clubFixtures->pluck('id'));
        });
    }
}
