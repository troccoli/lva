<?php

namespace Tests\Unit\Models;

use LVA\Models\Fixture;
use LVA\Models\MappedVenue;
use LVA\Models\Venue;
use LVA\Models\VenueSynonym;
use Tests\TestCase;

/**
 * Class VenueTest.
 */
class VenueTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_found_by_name()
    {
        /** @var Venue[] $venues */
        $venues = factory(Venue::class)->times(2)->create();

        // I have to use the toArray() method as I'm only interested in the table's fields and not any internal ones
        $this->assertEquals($venues[0]->toArray(), Venue::findByName($venues[0]->venue)->toArray());
        $this->assertNull(Venue::findByName($venues[0]->venue.'--'));
        $this->assertEquals($venues[1]->toArray(), Venue::findByName($venues[1]->venue)->toArray());
        $this->assertNull(Venue::findByName($venues[1]->venue.'--'));
    }

    /**
     * @test
     */
    public function it_has_many_fixtures()
    {
        // Random number of fixtures to create
        $fixtures = $this->faker->numberBetween(2, 10);

        /** @var Venue[] $venues */
        $venues = factory(Venue::class)->times(2)->create();

        // Create a random number of fixture for the first team as away team
        factory(Fixture::class, $fixtures)->times($fixtures)->create(['venue_id' => $venues[0]->id]);

        $this->assertCount(0, $venues[1]->fixtures);
        $this->assertCount($fixtures, $venues[0]->fixtures);
    }

    /**
     * @test
     */
    public function it_has_many_synonyms()
    {
        /** @var Venue[] $venues */
        $venues = factory(Venue::class)->times(2)->create();

        $synonyms = $this->faker->numberBetween(2, 20);
        factory(VenueSynonym::class)->times($synonyms)->create(['venue_id' => $venues[0]->getId()]);

        $this->assertCount($synonyms, $venues[0]->synonyms);
        $this->assertCount(0, $venues[1]->synonyms);
    }

    /**
     * @test
     */
    public function it_has_many_mapped_venues()
    {
        /** @var Venue[] $venues */
        $venues = factory(Venue::class)->times(2)->create();

        $mapped = $this->faker->numberBetween(2, 20);
        factory(MappedVenue::class)->times($mapped)->create(['venue_id' => $venues[0]->getId()]);

        $this->assertCount($mapped, $venues[0]->mapped);
        $this->assertCount(0, $venues[1]->mapped);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $this->assertEquals($venue->id, $venue->getId());
    }

    /**
     * @test
     */
    public function it_gets_the_name()
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $this->assertEquals($venue->venue, $venue->getName());
    }

    /**
     * @test
     */
    public function it_gets_the_directions()
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $this->assertEquals($venue->directions, $venue->getDirections());
    }

    /**
     * @test
     */
    public function it_gets_the_postcode()
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $this->assertEquals($venue->postcode, $venue->getPostcode());
    }

    /**
     * @test
     */
    public function it_is_a_string()
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        $this->assertEquals($venue->venue, (string) $venue);
    }
}
