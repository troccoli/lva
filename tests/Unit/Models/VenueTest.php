<?php

namespace Tests\Unit\Models;

use App\Models\Club;
use App\Models\Fixture;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class VenueTest extends TestCase
{
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

        // Other clubs
        $anotherVenue = factory(Venue::class)->create();
        aClub()->withVenue($anotherVenue)->build();
        aClub()->withVenue($anotherVenue)->build();
        aClub()->withVenue($anotherVenue)->build();
        aClub()->withVenue($anotherVenue)->build();
        aClub()->withVenue($anotherVenue)->build();

        $this->assertCount(3, $venue->getClubs());
        $clubs->each(function (Club $club) use ($venue): void {
            $this->assertTrue($venue->getClubs()->contains($club));
        });
    }

    public function testItGetsTheFixtures(): void
    {
        $venue = factory(Venue::class)->create();
        $fixtures = collect([
            aFixture()->at($venue)->build(),
            aFixture()->at($venue)->build(),
            aFixture()->at($venue)->build(),
            aFixture()->at($venue)->build(),
            aFixture()->at($venue)->build(),
        ]);

        // Other fixtures
        $anotherVenue = factory(Venue::class)->create();
        aFixture()->at($anotherVenue)->build();
        aFixture()->at($anotherVenue)->build();
        aFixture()->at($anotherVenue)->build();
        aFixture()->at($anotherVenue)->build();

        /** @var Collection $fixturesAtVenue */
        $fixturesAtVenue = $venue->getFixtures();

        $this->assertCount(5, $fixturesAtVenue);

        $fixtures->each(function (Fixture $fixture) use ($fixturesAtVenue): void {
            $this->assertContains($fixture->getId(), $fixturesAtVenue->pluck('id'));
        });
    }
}
