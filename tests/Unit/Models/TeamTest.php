<?php

namespace Tests\Unit\Models;

use LVA\Models\Club;
use LVA\Models\Fixture;
use LVA\Models\MappedTeam;
use LVA\Models\Team;
use LVA\Models\TeamSynonym;
use Tests\TestCase;

/**
 * Class TeamTest.
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

        // I have to use the toArray() method as I'm only interested in the table's fields and not any internal ones
        $this->assertEquals($teams[0]->toArray(), Team::findByName($teams[0]->team)->toArray());
        $this->assertNull(Team::findByName($teams[0]->team.'--'));
        $this->assertEquals($teams[1]->toArray(), Team::findByName($teams[1]->team)->toArray());
        $this->assertNull(Team::findByName($teams[1]->team.'--'));
    }

    /**
     * @test
     */
    public function it_can_be_found_by_trigram()
    {
        /** @var Team[] $teams */
        $teams = factory(Team::class)->times(2)->create();

        // I have to use the toArray() method as I'm only interested in the table's fields and not any internal ones
        $this->assertEquals($teams[0]->toArray(), Team::findByTrigram($teams[0]->trigram)->toArray());
        $this->assertNull(Team::findByTrigram($teams[0]->trigram.'--'));
        $this->assertEquals($teams[1]->toArray(), Team::findByTrigram($teams[1]->trigram)->toArray());
        $this->assertNull(Team::findByTrigram($teams[1]->trigram.'--'));
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
        $fixtures = $this->faker->numberBetween(2, 10);

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
        $fixtures = $this->faker->numberBetween(2, 10);

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
        /** @var Team[] $teams */
        $teams = factory(Team::class)->times(2)->create();

        $synonyms = $this->faker->numberBetween(2, 20);
        factory(TeamSynonym::class)->times($synonyms)->create(['team_id' => $teams[0]->getId()]);

        $this->assertCount($synonyms, $teams[0]->synonyms);
        $this->assertCount(0, $teams[1]->synonyms);
    }

    /**
     * @test
     */
    public function it_has_many_mapped_teams()
    {
        /** @var Team[] $teams */
        $teams = factory(Team::class)->times(2)->create();

        $mapped = $this->faker->numberBetween(2, 20);
        factory(MappedTeam::class)->times($mapped)->create(['team_id' => $teams[0]->getId()]);

        $this->assertCount($mapped, $teams[0]->mapped);
        $this->assertCount(0, $teams[1]->mapped);
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
    public function it_gets_the_name()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->assertEquals($team->team, $team->getName());
    }

    /**
     * @test
     */
    public function it_gets_the_trigram()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->assertEquals($team->trigram, $team->getTrigram());
    }

    /**
     * @test
     */
    public function it_always_stores_uppercase_trigrams()
    {
        $trigram = $this->faker->lexify('???');
        /** @var Team $team */
        $team = factory(Team::class)->create(['trigram' => strtolower($trigram)]);

        $this->assertEquals(strtoupper($trigram), $team->getTrigram());
    }

    /**
     * @test
     */
    public function it_is_a_string()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->assertEquals($team->team, (string) $team);
    }
}
