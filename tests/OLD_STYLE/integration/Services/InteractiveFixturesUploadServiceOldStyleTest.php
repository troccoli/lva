<?php

namespace Tests\Services;

use Tests\OldStyleTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class InteractiveFixturesUploadServiceTest
 *
 * @package Tests
 */
class InteractiveFixturesUploadServiceOldStyleTest extends OldStyleTestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_creates_a_job()
    {
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
