<?php

namespace Tests\Models;

use LVA\Models\Team;
use LVA\Models\TeamSynonym;
use Tests\TestCase;

/**
 * Class TeamSynonymTest
 *
 * @package Tests\Models
 */
class TeamSynonymTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_find_the_team_by_synonym()
    {
        /** @var TeamSynonym[] $teamSynonyms */
        $teamSynonyms = factory(TeamSynonym::class)->times(2)->create();

        $this->assertEquals($teamSynonyms[0]->team, TeamSynonym::findBySynonym($teamSynonyms[0]->getSynonym()));
        $this->assertNull(TeamSynonym::findBySynonym($teamSynonyms[0]->getSynonym() . '---'));
        $this->assertEquals($teamSynonyms[1]->team, TeamSynonym::findBySynonym($teamSynonyms[1]->getSynonym()));
        $this->assertNull(TeamSynonym::findBySynonym($teamSynonyms[1]->getSynonym() . '---'));
    }

    /**
     * @test
     */
    public function it_belongs_to_a_team()
    {
        /** @var TeamSynonym $teamSynonym */
        $teamSynonym = factory(TeamSynonym::class)->create();

        $this->assertInstanceOf(Team::class, $teamSynonym->team);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var TeamSynonym $teamSynonym */
        $teamSynonym = factory(TeamSynonym::class)->create();

        $this->assertEquals($teamSynonym->id, $teamSynonym->getId());
    }

    /**
     * @test
     */
    public function it_gets_the_synonym()
    {
        /** @var TeamSynonym $teamSynonym */
        $teamSynonym = factory(TeamSynonym::class)->create();

        $this->assertEquals($teamSynonym->synonym, $teamSynonym->getSynonym());
    }

    /**
     * @test
     */
    public function it_sets_the_synonym()
    {
        /** @var TeamSynonym $teamSynonym */
        $teamSynonym = factory(TeamSynonym::class)->create();

        $this->assertEquals($teamSynonym->synonym, $teamSynonym->getSynonym());

        $newSynonym = str_random();
        $teamSynonym->setSynonym($newSynonym);
        $this->assertEquals($newSynonym, $teamSynonym->getSynonym());
    }

    /**
     * @test
     */
    public function it_sets_the_team()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var TeamSynonym $teamSynonym */
        $teamSynonym = factory(TeamSynonym::class)->create();

        $this->assertNotEquals($team->getId(), $teamSynonym->team->getId());

        $teamSynonym->setTeam($team->getName());
        $this->assertEquals($team->getId(), $teamSynonym->team->getId());
    }
}
