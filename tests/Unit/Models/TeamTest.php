<?php

namespace Tests\Unit\Models;

use App\Models\Club;
use App\Models\Team;
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
}
