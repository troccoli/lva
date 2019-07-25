<?php

namespace Tests\Unit\Models;

use App\Models\Competition;
use App\Models\Club;
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
}
