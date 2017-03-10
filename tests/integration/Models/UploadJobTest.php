<?php

namespace Tests\Models;

use Faker\Factory;
use LVA\Models\Season;
use LVA\Models\UploadJobStatus;
use Tests\TestCase;
use LVA\Models\UploadJob;

/**
 * Class UploadJobTest
 *
 * @package Tests\Models
 */
class UploadJobTest extends TestCase
{
    /** @var \Faker\Generator */
    private $faker;

    protected function setUp()
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * @test
     */
    public function it_has_many_mapped_teams()
    {
        $this->markTestIncomplete('Need to create the factory for the MappedTeam.');
    }

    /**
     * @test
     */
    public function it_has_many_mapped_venues()
    {
        $this->markTestIncomplete('Need to create the factory for MappedVenue.');

    }

    /**
     * @test
     */
    public function it_has_many_new_venues()
    {
        $this->markTestIncomplete('Need to create the factory for NewVenue.');
    }

    /**
     * @test
     */
    public function it_has_many_data()
    {
        $this->markTestIncomplete('Need to create the factory for UploadJobsData.');
    }

    /**
     * @test
     */
    public function it_returns_stale_jobs()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $this->assertEquals($job->id, $job->getId());
    }

    /**
     * @test
     */
    public function it_gets_the_associated_season()
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create(['season_id' => $season->getId()]);

        $this->assertEquals($season->getId(), $job->getSeason());
    }

    /**
     * @test
     */
    public function it_sets_the_associated_season()
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $this->assertNotEquals($season->getId(), $job->getSeason());

        $job->setSeason($season->getId());
        $this->assertEquals($season->getId(), $job->getSeason());
    }

    /**
     * @test
     */
    public function it_gets_the_file()
    {
        $file = $this->faker->file('/', '/tmp', false);

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create(['file' => $file]);

        $this->assertEquals($file, $job->getFile());
    }

    /**
     * @test
     */
    public function it_sets_the_file()
    {
        $faker = Factory::create();
        $file = $faker->file('/', '/tmp', false);

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $this->assertNotEquals($file, $job->getFile());

        $job->setFile($file);
        $this->assertEquals($file, $job->getFile());
    }

    /**
     * @test
     */
    public function it_gets_the_job_type()
    {
        $type = $this->faker->word;

        // DO NOT USE create() as the DB only accept 'fixture' as a type
        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->make(['type' => $type]);

        $this->assertEquals($type, $job->getType());
    }

    /**
     * @test
     */
    public function it_sets_the_job_type()
    {
        $type = $this->faker->word;

        // DO NOT USE create() as the DB only accept 'fixture' as a type
        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->make();

        $this->assertNotEquals($type, $job->getType());

        $job->setType($type);
        $this->assertEquals($type, $job->getType());
    }

    /**
     * @test
     */
    public function it_gets_the_status()
    {
        $this->markTestIncomplete('To be fixed.');
        // The UploadJobStatus does not have an Eloquent model, so we cannot use create()
        /** @var UploadJobStatus $status */
        $status = factory(UploadJobStatus::class)->make();

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create(['status' => json_encode($status->toArray())]);

        $this->assertEquals($status->toArray(), $job->getStatus());
    }

    /**
     * @test
     */
    public function it_sets_the_status()
    {
        $this->markTestIncomplete('To be fixed.');
        // The UploadJobStatus does not have an Eloquent model, so we cannot use create()
        /** @var UploadJobStatus $status */
        $status = factory(UploadJobStatus::class)->make();

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $this->assertNotEquals($status->toArray(), $job->getStatus());

        $job->setStatus($status->toArray());
        $this->assertEquals($status->toArray(), $job->getStatus());
    }

    /**
     * @test
     */
    public function it_store_the_status_as_json()
    {
        $this->markTestIncomplete('To be fixed.');
        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        dd($job);
        $status = \DB::table($job->getTable())->where('id', $job->getId())->first(['status']);

        dd($status->status);
        $this->assertJson($status->status);
    }

    /**
     * @test
     */
    public function it_gets_the_total_row_number()
    {
    }

    /**
     * @test
     */
    public function it_sets_the_total_row_number()
    {
    }


}
