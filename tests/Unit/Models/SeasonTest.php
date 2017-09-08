<?php

namespace Tests\Unit\Models;

use LVA\Models\Division;
use LVA\Models\Season;
use Tests\TestCase;

/**
 * Class SeasonTest
 *
 * @package Tests\Unit\Models
 */
class SeasonTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_many_divisions()
    {
        // random number of divisions
        $divisions = $this->faker->numberBetween(2, 10);

        /** @var Season[] $seasons */
        $seasons = factory(Season::class)->times(2)->create();

        // create a random number of division for the first season
        factory(Division::class)->times($divisions)->create(['season_id' => $seasons[0]->getId()]);

        $this->assertCount(0, $seasons[1]->divisions);
        $this->assertCount($divisions, $seasons[0]->divisions);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();

        $this->assertEquals($season->id, $season->getId());
    }

    /**
     * @test
     */
    public function it_is_a_string()
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();

        $this->assertEquals($season->season, (string)$season);

    }
}
