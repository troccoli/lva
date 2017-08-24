<?php

namespace Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use LVA\Models\Fixture;
use LVA\Models\TeamSynonym;
use LVA\Models\UploadJob;
use LVA\Models\UploadJobData;
use LVA\Models\VenueSynonym;
use Tests\TestCase;

/**
 * Class UploadJobDataTest
 *
 * @package Tests\Integration\Models
 */
class UploadJobDataTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_can_find_the_data_by_job()
    {
        /** @var UploadJob[] $jobs */
        $jobs = factory(UploadJob::class)->times(2)->create();

        $teamSynonyms = mt_rand(1, 10);
        factory(UploadJobData::class,
            TeamSynonym::class)->times($teamSynonyms)->create(['upload_job_id' => $jobs[0]->getId()]);

        $venueSynonyms = mt_rand(1, 10);
        factory(UploadJobData::class,
            VenueSynonym::class)->times($venueSynonyms)->create(['upload_job_id' => $jobs[0]->getId()]);

        $fixtures = mt_rand(2, 20);
        factory(UploadJobData::class, Fixture::class)->times($fixtures)->create(['upload_job_id' => $jobs[0]->getId()]);

        $this->assertCount($teamSynonyms + $venueSynonyms + $fixtures, UploadJobData::findByJobId($jobs[0]->getId()));
        $this->assertCount(0, UploadJobData::findByJobId($jobs[1]->getId()));
    }

    /**
     * @test
     */
    public function it_belongs_to_a_job()
    {
        /** @var UploadJobData $data */
        $data = factory(UploadJobData::class)->create();

        $this->assertInstanceOf(UploadJob::class, $data->uploadJob);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var UploadJobData $data */
        $data = factory(UploadJobData::class)->create();

        $this->assertEquals($data->id, $data->getId());
    }

    /**
     * @test
     */
    public function it_sets_the_job_id()
    {
        /** @var UploadJobData $data */
        $data = factory(UploadJobData::class)->create();

        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        $this->assertNotEquals($job->toArray(), $data->uploadJob->toArray());

        $data->setJob($job->getId());
        $this->assertEquals($job->toArray(), $data->uploadJob->toArray());
    }

    /**
     * @test
     */
    public function it_gets_the_model()
    {
        /** @var UploadJobData $data */
        $data = factory(UploadJobData::class)->create();

        $this->assertEquals($data->model, $data->getModel());
    }

    /**
     * @test
     */
    public function it_sets_the_model()
    {
        /** @var UploadJobData $data */
        $data = factory(UploadJobData::class, Fixture::class)->create();

        /** @var string $model */
        $model = TeamSynonym::class;

        $this->assertNotEquals($model, $data->getModel());

        $data->setModel($model);
        $this->assertEquals($model, $data->getModel());
    }

    /**
     * @test
     */
    public function it_gets_the_data()
    {
        /** @var UploadJobData $data */
        $data = factory(UploadJobData::class)->create();

        $this->assertEquals($data->model_data, $data->getData());
    }

    /**
     * @test
     */
    public function it_sets_the_data()
    {
        /** @var UploadJobData $data */
        $data = factory(UploadJobData::class)->create();

        /** @var Fixture $fixture */
        $fixture = factory(Fixture::class)->create();

        $this->assertNotEquals(serialize($fixture), $data->getData());

        $data->setData(serialize($fixture));
        $this->assertEquals(serialize($fixture), $data->getData());
    }
}
