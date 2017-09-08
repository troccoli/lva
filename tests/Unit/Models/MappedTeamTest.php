<?php

namespace Tests\Unit\Models;

use LVA\Models\MappedTeam;
use LVA\Models\Team;
use LVA\Models\UploadJob;
use Tests\TestCase;

/**
 * Class MappedTeamTest
 *
 * @package Tests\Unit\Models
 */
class MappedTeamTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_find_the_teams_by_job()
    {
        /** @var UploadJob[] $jobs */
        $jobs = factory(UploadJob::class)->times(2)->create();

        $teams = $this->faker->numberBetween(2, 20);
        factory(MappedTeam::class)->times($teams)->create(['upload_job_id' => $jobs[0]->getId()]);

        $this->assertCount($teams, MappedTeam::findByJob($jobs[0]->getId()));
        $this->assertCount(0, MappedTeam::findByJob($jobs[1]->getId()));
    }

    /**
     * @test
     */
    public function it_belongs_to_a_job()
    {
        /** @var MappedTeam $mappedTeam */
        $mappedTeam = factory(MappedTeam::class)->create();

        $this->assertInstanceOf(UploadJob::class, $mappedTeam->uploadJob);
    }

    /**
     * @test
     */
    public function it_belong_to_a_team()
    {
        /** @var MappedTeam $mappedTeam */
        $mappedTeam = factory(MappedTeam::class)->create();

        $this->assertInstanceOf(Team::class, $mappedTeam->team);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var MappedTeam $mappedTeam */
        $mappedTeam = factory(MappedTeam::class)->create();

        $this->assertEquals($mappedTeam->id, $mappedTeam->getId());
    }

    /**
     * @test
     */
    public function it_sets_the_job()
    {
        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        /** @var MappedTeam $mappedTeam */
        $mappedTeam = factory(MappedTeam::class)->create();

        $this->assertNotEquals($job->getId(), $mappedTeam->upload_job_id);

        $mappedTeam->setUploadJob($job->getId());
        $this->assertEquals($job->getId(), $mappedTeam->upload_job_id);
    }

    /**
     * @test
     */
    public function it_gets_the_name_of_the_mapped_team()
    {
        /** @var MappedTeam $mappedTeam */
        $mappedTeam = factory(MappedTeam::class)->create();

        $this->assertEquals($mappedTeam->mapped_team, $mappedTeam->getName());
    }

    /**
     * @test
     */
    public function it_sets_the_name_of_the_mapped_team()
    {
        /** @var MappedTeam $mappedTeam */
        $mappedTeam = factory(MappedTeam::class)->create();

        $this->assertEquals($mappedTeam->mapped_team, $mappedTeam->getName());

        $newName = $this->faker->unique()->name;
        $this->assertNotEquals($newName, $mappedTeam->getName());
        $mappedTeam->setName($newName);
        $this->assertEquals($newName, $mappedTeam->mapped_team);
    }

    /**
     * @test
     */
    public function it_sets_the_team_it_is_mapped_to()
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        /** @var MappedTeam $mappedTeam */
        $mappedTeam = factory(MappedTeam::class)->create();

        $this->assertNotEquals($team->getId(), $mappedTeam->team->id);

        $mappedTeam->setMappedTeam($team->getName());
        $this->assertEquals($team->getId(), $mappedTeam->team->getId());
    }
}
