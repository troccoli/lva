<?php

namespace Tests\Unit\Models;

use App\Models\Competition;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DivisionTest extends TestCase
{
    use RefreshDatabase;

    public function testItGetsTheId(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $this->assertEquals($division->id, $division->getId());
    }

    public function testItGetsTheName(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $this->assertEquals($division->name, $division->getName());
    }

    public function testItGetsTheDisplayingOrder(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $this->assertEquals($division->display_order, $division->getOrder());
    }

    public function testItGetsTheCompetition(): void
    {
        $competition = factory(Competition::class)->create();
        /** @var Division $division */
        $division = factory(Division::class)->create(['competition_id' => $competition->getId()]);
        $this->assertEquals($competition->getId(), $division->getCompetition()->getId());
    }
}
