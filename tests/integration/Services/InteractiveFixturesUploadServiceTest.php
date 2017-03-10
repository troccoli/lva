<?php

namespace Tests\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
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
    }
}
