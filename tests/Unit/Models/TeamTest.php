<?php

namespace Tests\Unit\Models;

use App\Models\Club;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase;

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

    public function testItGetsTheClub(): void
    {
        $club = factory(Club::class)->create();
        /** @var Team $team */
        $team = aTeam()->inClub($club)->build();
        $this->assertEquals($club->getId(), $team->getClub()->getId());
    }

    public function testItGetsTheVenue(): void
    {
        $olympicStadium = factory(Venue::class)->create(['name' => 'Olympic Stadium']);
        $team = aTeam()->withVenue($olympicStadium)->build();

        $this->assertEquals($olympicStadium->toArray(), $team->getVenue()->toArray());

        $club = aClub()->withVenue($olympicStadium)->build();
        $team = aTeam()->inClub($club)->build();

        $this->assertEquals($olympicStadium->toArray(), $team->getVenue()->toArray());
    }

    public function testItGetsTheVenueFromTheClub(): void
    {
        $venue = factory(Venue::class)->create(['name' => 'Olympic Stadium']);
        $club = aClub()->withVenue($venue)->build();
        $team = aTeam()->inClub($club)->build();

        $this->assertEquals($venue->toArray(), $team->getVenue()->toArray());
    }

    public function testItGetsTheVenueId(): void
    {
        $venue = factory(Venue::class)->create(['name' => 'Olympic Stadium']);
        $team = aTeam()->withVenue($venue)->build();

        $this->assertSame($venue->getId(), $team->getVenueId());
    }

    public function testItDoesNotGetTheVenueIdFromTheClub(): void
    {
        $venue = factory(Venue::class)->create(['name' => 'Olympic Stadium']);
        $club = aClub()->withVenue($venue)->build();
        $team = aTeam()->inClub($club)->build();

        $this->assertNull($team->getVenueId());
    }
}
