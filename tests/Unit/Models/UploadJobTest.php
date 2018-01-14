<?php

namespace Tests\Unit\Models;

use Carbon\Carbon;
use LVA\Models\MappedTeam;
use LVA\Models\MappedVenue;
use LVA\Models\Season;
use LVA\Models\UploadJob;
use LVA\Models\UploadJobData;
use LVA\Models\UploadJobStatus;
use Tests\TestCase;

/**
 * Class UploadJobTest.
 */
class UploadJobTest extends TestCase
{
    /**
     * @test
     */
    public function it_has_many_mapped_teams()
    {
        /** @var UploadJob[] $jobs */
        $jobs = factory(UploadJob::class)->times(2)->create();

        $teams = $this->faker->numberBetween(2, 10);
        factory(MappedTeam::class)->times($teams)->create(['upload_job_id' => $jobs[0]->getId()]);

        $this->assertCount($teams, $jobs[0]->mappedTeams);
        $this->assertCount(0, $jobs[1]->mappedTeams);
    }

    /**
     * @test
     */
    public function it_has_many_mapped_venues()
    {
        /** @var UploadJob[] $jobs */
        $jobs = factory(UploadJob::class)->times(2)->create();

        $venues = $this->faker->numberBetween(2, 10);
        factory(MappedVenue::class)->times($venues)->create(['upload_job_id' => $jobs[0]->getId()]);

        $this->assertCount($venues, $jobs[0]->mappedVenues);
        $this->assertCount(0, $jobs[1]->mappedVenues);
    }

    /**
     * @test
     */
    public function it_has_many_data()
    {
        /** @var UploadJob[] $jobs */
        $jobs = factory(UploadJob::class)->times(2)->create();

        $data = $this->faker->numberBetween(2, 10);
        factory(UploadJobData::class)->times($data)->create(['upload_job_id' => $jobs[0]->getId()]);

        $this->assertCount($data, $jobs[0]->uploadData);
        $this->assertCount(0, $jobs[1]->uploadData);
    }

    /**
     * @test
     */
    public function it_returns_stale_jobs()
    {
        $staleDate = Carbon::now()->subWeek();

        $jobs = $this->faker->numberBetween(2, 20);
        factory(UploadJob::class)->times($jobs)->create(['created_at' => $staleDate, 'updated_at' => $staleDate]);

        $this->assertCount($jobs, UploadJob::stale()->get());
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
        $file = $this->faker->file('/', '/tmp', false);

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
        $type = str_random(10);

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
        $type = str_random(10);

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
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create(['status' => json_encode($status->toArray())]);

        $this->assertEquals($status->toArray(), $job->getStatus());
    }

    /**
     * @test
     */
    public function it_sets_the_status()
    {
        /** @var UploadJobStatus $status */
        $status = $this->uploadJobTestFactory();

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
        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $status = \DB::table($job->getTable())->where('id', $job->getId())->first(['status']);

        $this->assertJson($status->status);
    }

    /**
     * @test
     */
    public function it_gets_the_total_row_number()
    {
        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $this->assertEquals($job->row_count, $job->getRowCount());
    }

    /**
     * @test
     */
    public function it_sets_the_total_row_number()
    {
        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $rows = $job->getRowCount() + $this->faker->numberBetween(1, 10);

        $this->assertNotEquals($rows, $job->getRowCount());

        $job->setRowCount($rows);
        $this->assertEquals($rows, $job->getRowCount());
    }
}
