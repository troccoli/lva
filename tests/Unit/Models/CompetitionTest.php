<?php

namespace Tests\Unit;

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionTest extends TestCase
{
    use RefreshDatabase;

    public function testItGetsTheId(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $this->assertEquals($competition->id, $competition->getId());
    }

    public function testItGetsTheName(): void
    {
        /** @var Competition $competions */
        $competions = factory(Competition::class)->create();
        $this->assertEquals($competions->name, $competions->getName());
    }

    public function testItGetsTheSeason(): void
    {
        $season = factory(Season::class)->create();
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create(['season_id' => $season->id]);
        $this->assertEquals($season->getId(), $competition->getSeason()->getId());
    }
}
