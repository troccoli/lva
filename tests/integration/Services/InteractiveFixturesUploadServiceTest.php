<?php

namespace Tests\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use LVA\Models\Season;
use LVA\Services\InteractiveFixturesUploadService;

/**
 * Class InteractiveFixturesUploadServiceTest
 *
 * @package Tests
 */
class InteractiveFixturesUploadServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_creates_a_job()
    {
        $season = factory(Season::class)->create();
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function it_can_process_a_job()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function it_can_clean_up()
    {
        $this->markTestIncomplete();
    }
}
