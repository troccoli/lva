<?php

namespace Tests\Unit\Models;

use LVA\Models\MappedVenue;
use LVA\Models\UploadJob;
use LVA\Models\Venue;
use Tests\TestCase;

/**
 * Class MappedVenueTest
 *
 * @package Tests\Unit\Models
 */
class MappedVenueTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_find_the_venues_by_job()
    {
        /** @var UploadJob[] $jobs */
        $jobs = factory(UploadJob::class)->times(2)->create();

        $venues = $this->faker->numberBetween(2, 20);
        factory(MappedVenue::class)->times($venues)->create(['upload_job_id' => $jobs[0]->getId()]);

        $this->assertCount($venues, MappedVenue::findByJob($jobs[0]->getId()));
        $this->assertCount(0, MappedVenue::findByJob($jobs[1]->getId()));
    }

    /**
     * @test
     */
    public function it_belongs_to_a_job()
    {
        /** @var MappedVenue $mappedVenue */
        $mappedVenue = factory(MappedVenue::class)->create();

        $this->assertInstanceOf(UploadJob::class, $mappedVenue->uploadJob);
    }

    /**
     * @test
     */
    public function it_belong_to_a_venue()
    {
        /** @var MappedVenue $mappedVenue */
        $mappedVenue = factory(MappedVenue::class)->create();

        $this->assertInstanceOf(Venue::class, $mappedVenue->venue);
    }

    /**
     * @test
     */
    public function it_gets_the_id()
    {
        /** @var MappedVenue $mappedVenue */
        $mappedVenue = factory(MappedVenue::class)->create();

        $this->assertEquals($mappedVenue->id, $mappedVenue->getId());
    }

    /**
     * @test
     */
    public function it_sets_the_job()
    {
        /** @var UploadJob $job */
        $job = factory(UploadJob::class)->create();

        /** @var MappedVenue $mappedVenue */
        $mappedVenue = factory(MappedVenue::class)->create();

        $this->assertNotEquals($job->getId(), $mappedVenue->upload_job_id);

        $mappedVenue->setUploadJob($job->getId());
        $this->assertEquals($job->getId(), $mappedVenue->upload_job_id);
    }

    /**
     * @test
     */
    public function it_gets_the_name_of_the_mapped_venue()
    {
        /** @var MappedVenue $mappedVenue */
        $mappedVenue = factory(MappedVenue::class)->create();

        $this->assertEquals($mappedVenue->mapped_venue, $mappedVenue->getName());
    }

    /**
     * @test
     */
    public function it_sets_the_name_of_the_mapped_tesm()
    {
        /** @var MappedVenue $mappedVenue */
        $mappedVenue = factory(MappedVenue::class)->create();

        $this->assertEquals($mappedVenue->mapped_venue, $mappedVenue->getName());

        $newName = $this->faker->unique()->name;
        $this->assertNotEquals($newName, $mappedVenue->getName());
        $mappedVenue->setName($newName);
        $this->assertEquals($newName, $mappedVenue->mapped_venue);
    }

    /**
     * @test
     */
    public function it_sets_the_venue_it_is_mapped_to()
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();

        /** @var MappedVenue $mappedVenue */
        $mappedVenue = factory(MappedVenue::class)->create();

        $this->assertNotEquals($venue->getId(), $mappedVenue->venue->id);

        $mappedVenue->setMappedVenue($venue->getName());
        $this->assertEquals($venue->getId(), $mappedVenue->venue->getId());
    }
}
