<?php

namespace Tests\Unit\Models;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ClubTest extends TestCase
{
    public function testItGetsTheId(): void
    {
        $club = Club::factory()->create();
        $this->assertEquals($club->id, $club->getId());
    }

    public function testItGetsTheName(): void
    {
        $club = Club::factory()->create();
        $this->assertEquals($club->name, $club->getName());
    }

    public function testItGetsTheNameOfTheSecretaryRole(): void
    {
        /** @var Club $club */
        $club = Club::factory()->create();
        $this->assertEquals("Club {$club->getId()} Secretary", RolesHelper::clubSecretary($club));
    }

    public function testItGetsTheTeams(): void
    {
        $club = Club::factory()->create();

        $teams = collect(
            [
                Team::factory()->for($club)->create(),
                Team::factory()->for($club)->create(),
                Team::factory()->for($club)->create(),
            ]
        );

        // Other teams
        $anotherClub = Club::factory()->create();
        Team::factory()->for($anotherClub)->create();
        Team::factory()->for($anotherClub)->create();
        Team::factory()->for($anotherClub)->create();
        Team::factory()->for($anotherClub)->create();

        $this->assertCount(3, $club->getTeams());
        $teams->each(
            function (Team $team) use ($club): void {
                $this->assertTrue($club->getTeams()->contains($team));
            }
        );
    }

    public function testItGetsTheVenue(): void
    {
        $venue = Venue::factory()->create();
        $club = Club::factory()->for($venue)->create();

        $this->assertEquals($venue->toArray(), $club->getVenue()->toArray());
    }

    public function testItGetsTheVenueId(): void
    {
        $venue = Venue::factory()->create();
        $club = Club::factory()->for($venue)->create();

        $this->assertSame($venue->getId(), $club->getVenueId());
    }

    public function testItGetsTheFixtures(): void
    {
        $club = Club::factory()->create();
        /** @var Team $team1 */
        $team1 = Team::factory()->for($club)->create();
        /** @var Team $team2 */
        $team2 = Team::factory()->for($club)->create();
        $fixtures = collect(
            [
                Fixture::factory()->between($team1, Team::factory()->create())->create(),
                Fixture::factory()->between($team1, Team::factory()->create())->create(),
                Fixture::factory()->between($team2, Team::factory()->create())->create(),
                Fixture::factory()->between($team2, Team::factory()->create())->create(),
                Fixture::factory()->between(Team::factory()->create(), $team2)->create(),
                Fixture::factory()->between(Team::factory()->create(), $team1)->create(),
                Fixture::factory()->between(Team::factory()->create(), $team2)->create(),
            ]
        );

        // Other fixtures
        Fixture::factory()->create();
        Fixture::factory()->create();
        Fixture::factory()->create();
        Fixture::factory()->create();
        Fixture::factory()->create();

        /** @var Collection $clubFixtures */
        $clubFixtures = $club->getFixtures();

        $this->assertCount(7, $clubFixtures);
        $fixtures->each(
            function (Fixture $fixture) use ($clubFixtures): void {
                $this->assertContains($fixture->getId(), $clubFixtures->pluck('id'));
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
