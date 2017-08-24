<?php

namespace Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use LVA\Models\Venue;
use LVA\Models\VenueSynonym;
use Tests\TestCase;

/**
 * Class VenueSynonyms
 *
 * @package Tests\Integration\Models
 */
class VenueSynonyms extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_can_find_the_venue_by_synonym()
    {
        /** @var VenueSynonym[] $venueSynonyms */
        $venueSynonyms = factory(VenueSynonym::class)->times(2)->create();

        $this->assertEquals($venueSynonyms[0]->venue, VenueSynonym::findBySynonym($venueSynonyms[0]->getSynonym()));
        $this->assertNull(VenueSynonym::findBySynonym($venueSynonyms[0]->getSynonym() . '---'));
        $this->assertEquals($venueSynonyms[1]->venue, VenueSynonym::findBySynonym($venueSynonyms[1]->getSynonym()));
        $this->assertNull(VenueSynonym::findBySynonym($venueSynonyms[1]->getSynonym() . '---'));
    }

    /**
     * @test
     */
    public function it_belongs_to_a_venue()
    {
        /** @var VenueSynonym $venueSynonym */
        $venueSynonym = factory(VenueSynonym::class)->create();

        $this->assertInstanceOf(Venue::class, $venueSynonym->venue);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var VenueSynonym $venueSynonym */
        $venueSynonym = factory(VenueSynonym::class)->create();

        $this->assertEquals($venueSynonym->id, $venueSynonym->getId());
    }

    /**
     * @test
     */
    public function it_gets_the_synonym()
    {
        /** @var VenueSynonym $venueSynonym */
        $venueSynonym = factory(VenueSynonym::class)->create();

        $this->assertEquals($venueSynonym->synonym, $venueSynonym->getSynonym());
    }

    /**
     * @test
     */
    public function it_sets_the_synonym()
    {
        /** @var VenueSynonym $venueSynonym */
        $venueSynonym = factory(VenueSynonym::class)->create();

        $this->assertEquals($venueSynonym->synonym, $venueSynonym->getSynonym());

        $newSynonym = str_random();
        $venueSynonym->setSynonym($newSynonym);
        $this->assertEquals($newSynonym, $venueSynonym->getSynonym());
    }

    /**
     * @test
     */
    public function it_sets_the_venue()
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        /** @var VenueSynonym $venueSynonym */
        $venueSynonym = factory(VenueSynonym::class)->create();

        $this->assertNotEquals($venue->getId(), $venueSynonym->venue->getId());

        $venueSynonym->setVenue($venue->getName());
        $this->assertEquals($venue->getId(), $venueSynonym->venue->getId());
    }
}
