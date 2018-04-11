<?php

namespace Tests\Unit\Models;

use LVA\Models\Club;
use LVA\Models\Team;
use Tests\TestCase;

/**
 * Class ClubTest.
 */
class ClubTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_many_teams()
    {
        // random number of teams to create
        $teams = $this->faker->numberBetween(2, 10);

        /** @var Club[] $clubs */
        $clubs = factory(Club::class)->times(2)->create();

        // Create the appointments for the first role;
        factory(Team::class)->times($teams)->create(['club_id' => $clubs[0]->id]);

        $this->assertCount(0, $clubs[1]->teams);
        $this->assertCount($teams, $clubs[0]->teams);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var Club $club */
        $club = factory(Club::class)->create();

        $this->assertEquals($club->id, $club->getId());
    }

    /**
     * @test
     */
    public function it_gets_the_name()
    {
        /** @var Club $club */
        $club = factory(Club::class)->create();

        $this->assertEquals($club->club, $club->getName());
    }

    /**
     * @test
     */
    public function it_is_a_string()
    {
        /** @var Club $club */
        $club = factory(Club::class)->create();

        $this->assertEquals($club->club, (string)$club);
    }
}
