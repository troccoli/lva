<?php

namespace Tests\Unit\Models;

use App\Models\Club;
use App\Models\Fixture;
use App\Models\Venue;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VenueTest extends TestCase
{
    public function testItGetsTheId(): void
    {
        $venue = Venue::factory()->create();
        $this->assertEquals($venue->id, $venue->getId());
    }

    public function testItGetsTheName(): void
    {
        $venue = Venue::factory()->create();
        $this->assertEquals($venue->name, $venue->getName());
    }

    public function testItGetsTheClubs(): void
    {
        $venue = Venue::factory()->create();

        $clubs = collect(
            [
                Club::factory()->for($venue)->create(),
                Club::factory()->for($venue)->create(),
                Club::factory()->for($venue)->create(),
            ]
        );

        $anotherVenue = Venue::factory()->create();
        Club::factory()->for($anotherVenue)->create();
        Club::factory()->for($anotherVenue)->create();
        Club::factory()->for($anotherVenue)->create();
        Club::factory()->for($anotherVenue)->create();
        Club::factory()->for($anotherVenue)->create();

        $this->assertCount(3, $venue->getClubs());
        $clubs->each(
            function (Club $club) use ($venue): void {
                $this->assertTrue($venue->getClubs()->contains($club));
            }
        );
    }

    public function testItGetsTheFixtures(): void
    {
        /** @var Venue $venue */
        $venue = Venue::factory()->create();
        $fixtures = collect(
            [
                Fixture::factory()->at($venue)->create(),
                Fixture::factory()->at($venue)->create(),
                Fixture::factory()->at($venue)->create(),
                Fixture::factory()->at($venue)->create(),
                Fixture::factory()->at($venue)->create(),
            ]
        );

        /** @var Venue $anotherVenue */
        $anotherVenue = Venue::factory()->create();
        Fixture::factory()->at($anotherVenue)->create();
        Fixture::factory()->at($anotherVenue)->create();
        Fixture::factory()->at($anotherVenue)->create();
        Fixture::factory()->at($anotherVenue)->create();

        $fixturesAtVenue = $venue->getFixtures();

        $this->assertCount(5, $fixturesAtVenue);

        $fixtures->each(
            function (Fixture $fixture) use ($fixturesAtVenue): void {
                $this->assertContains($fixture->getId(), $fixturesAtVenue->pluck('id'));
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
