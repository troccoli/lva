<?php

namespace Tests\Unit\Models;

use App\Models\Club;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueTest extends TestCase
{
    use RefreshDatabase;

    public function testItGetsTheId(): void
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $this->assertEquals($venue->id, $venue->getId());
    }

    public function testItGetsTheName(): void
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $this->assertEquals($venue->name, $venue->getName());
    }

    public function testItGetsTheClubs(): void
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $clubs = collect([
            aClub()->withVenue($venue)->build(),
            aClub()->withVenue($venue)->build(),
            aClub()->withVenue($venue)->build(),
        ]);
        aClub()->build();
        aClub()->build();
        aClub()->build();
        aClub()->build();

        $this->assertCount(3, $venue->getClubs());
        $clubs->each(function (Club $club) use ($venue): void {
            $this->assertTrue($venue->getClubs()->contains($club));
        });
    }
}
