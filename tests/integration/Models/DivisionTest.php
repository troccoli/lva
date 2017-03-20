<?php

namespace Tests\Models;

use LVA\Models\Fixture;
use LVA\Models\Season;
use LVA\Models\Division;
use Tests\TestCase;

/**
 * Class DivisionTest
 *
 * @package Tests\Models
 */
class DivisionTest extends TestCase
{
    /**
     * @test
     */
    public function it_belongs_to_one_season()
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();

        $this->assertInstanceOf(Season::class, $division->season);
    }

    /**
     * @test
     */
    public function it_has_many_fixtures()
    {
        // Random number of fixtures to create
        $fixtures = mt_rand(2, 10);

        /** @var Division[] $divisions */
        $divisions = factory(Division::class)->times(2)->create();

        // Create a random number of fixture for the first division
        factory(Fixture::class, $fixtures)->times($fixtures)->create(['division_id' => $divisions[0]->id]);

        $this->assertCount(0, $divisions[1]->fixtures);
        $this->assertCount($fixtures, $divisions[0]->fixtures);
    }

    /**
     * @test
     */
    public function it_can_be_found_by_name_in_a_season()
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();
        /** @var Division $division */
        $division = factory(Division::class)->create(['season_id' => $season->getId()]);

        // I have to use the toArray() method as I'm only interested in the table's fields and not any internal ones
        $this->assertEquals($division->toArray(), Division::findByName($season->getId(), $division->getName())->toArray());
        $this->assertNull(Division::findByName(0, $division->getName()));
        $this->assertNull(Division::findByName($season->getId(), $division->getName() . '--'));
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();

        $this->assertEquals($division->id, $division->getId());
    }

    /**
     * @test
     */
    public function it_gets_the_name()
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();

        $this->assertEquals($division->division, $division->getName());
    }

    /**
     * @test
     */
    public function it_is_a_string()
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();

        $this->assertEquals($division->season . ' ' . $division->division, (string)$division);

    }
}
