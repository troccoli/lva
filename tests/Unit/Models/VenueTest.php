<?php

namespace Tests\Unit\Models;

use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VenueTest extends TestCase
{
    use RefreshDatabase;

    public function testItGetsTheId(): void
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $this->assertEquals($venue->id, $venue->getId());
    }

    public function testItGetsTheName(): void
    {
        /** @var Venue $venue */
        $venue = factory(Venue::class)->create();
        $this->assertEquals($venue->name, $venue->getName());
    }
}
