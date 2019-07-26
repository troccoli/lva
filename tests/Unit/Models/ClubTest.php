<?php

namespace Tests\Unit\Models;

use App\Models\Club;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
