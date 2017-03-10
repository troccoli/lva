<?php

namespace Tests\Models;

use LVA\Models\Club;
use LVA\Models\Fixture;
use Tests\TestCase;
use LVA\Models\Team;

/**
 * Class TeamTest
 *
 * @package Tests\Models
 */
class TeamTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_found_by_name()
    {
        /** @var Team[] $teams */
        $teams = factory(Team::class)->times(2)->create();

        $this->assertEquals($teams[0]->getId(), Team::findByName($teams[0]->team)->getId());
        $this->assertNull(Team::findByName($teams[0]->team . '--'));
        $this->assertEquals($teams[1]->getId(), Team::findByName($teams[1]->team)->getId());
        $this->assertNull(Team::findByName($teams[1]->team . '--'));
    }

    /**
     * @test
     */
    public function it_belongs_to_a_club()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->assertInstanceOf(Club::class, $team->club);
    }

    /**
     * @test
     */
    public function it_has_many_fixtures_as_away_team()
    {
        // Random number of fixtures to create
        $fixtures = mt_rand(2, 10);

        /** @var Team[] $teams */
        $teams = factory(Team::class)->times(2)->create();

        // Create a random number of fixture for the first team as away team
        factory(Fixture::class, $fixtures)->times($fixtures)->create(['away_team_id' => $teams[0]->id]);

        $this->assertCount(0, $teams[1]->awayFixtures);
        $this->assertCount($fixtures, $teams[0]->awayFixtures);
    }

    /**
     * @test
     */
    public function it_has_many_fixtures_as_home_team()
    {
        // Random number of fixtures to create
        $fixtures = mt_rand(2, 10);

        /** @var Team[] $teams */
        $teams = factory(Team::class)->times(2)->create();

        // Create a random number of fixture for the first team as home team
        factory(Fixture::class, $fixtures)->times($fixtures)->create(['home_team_id' => $teams[0]->id]);

        $this->assertCount(0, $teams[1]->homeFixtures);
        $this->assertCount($fixtures, $teams[0]->homeFixtures);
    }

    /**
     * @test
     */
    public function it_has_many_synonyms()
    {
        $this->markTestIncomplete('Need to create the factory for team synonyms.');
    }

    /**
     * @test
     */
    public function it_has_many_mapped_teams()
    {
        $this->markTestIncomplete('Need to create the factory for the mapped teams.');
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->assertEquals($team->id, $team->getId());
    }

    /**
     * @test
     */
    public function it_is_a_string()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->assertEquals($team->team, (string)$team);
    }

}
