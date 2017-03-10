<?php

namespace Tests\Models;

use LVA\Models\Fixture;
use Tests\TestCase;
use LVA\Models\Venue;

/**
 * Class VenueTest
 *
 * @package Tests\Models
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

        $this->assertEquals($venues[0]->getId(), Venue::findByName($venues[0]->venue)->getId());
        $this->assertNull(Venue::findByName($venues[0]->venue . '--'));
        $this->assertEquals($venues[1]->getId(), Venue::findByName($venues[1]->venue)->getId());
        $this->assertNull(Venue::findByName($venues[1]->venue . '--'));
    }

    /**
     * @test
     */
    public function it_has_many_fixtures()
    {
        // Random number of fixtures to create
        $fixtures = mt_rand(2, 10);

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
        $this->markTestIncomplete('Need to create the factory for the mapped teams.');
    }

    /**
     * @test
     */
    public function it_has_many_mapped_venues()
    {
        $this->markTestIncomplete('Need to create the factory for the mapped teams.');
    }
}
