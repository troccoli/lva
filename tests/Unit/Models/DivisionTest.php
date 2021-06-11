<?php

namespace Tests\Unit\Models;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DivisionTest extends TestCase
{
    public function testItGetsTheId(): void
    {
        $division = Division::factory()->create();
        $this->assertEquals($division->id, $division->getId());
    }

    public function testItGetsTheName(): void
    {
        $division = Division::factory()->create();
        $this->assertEquals($division->name, $division->getName());
    }

    public function testItGetsTheNameOfTheAdminRole(): void
    {
        /** @var Division $division */
        $division = Division::factory()->create();
        $this->assertEquals("Division {$division->getId()} Administrator", RolesHelper::divisionAdmin($division));
    }

    public function testItGetsTheDisplayingOrder(): void
    {
        $division = Division::factory()->create();
        $this->assertEquals($division->display_order, $division->getOrder());
    }

    public function testItGetsTheCompetition(): void
    {
        $competition = Competition::factory()->create();
        $division = Division::factory()->create(['competition_id' => $competition->getId()]);
        $this->assertEquals($competition->getId(), $division->getCompetition()->getId());
    }

    public function testItGetsTheTeams(): void
    {
        $division = Division::factory()->create();
        $teams = collect(
            [
                Team::factory()->hasAttached($division)->create(),
                Team::factory()->hasAttached($division)->create(),
                Team::factory()->hasAttached($division)->create(),
                Team::factory()->hasAttached($division)->create(),
                Team::factory()->hasAttached($division)->create(),
            ]
        );

        $anotherDivision = Division::factory()->create();
        Team::factory()->hasAttached($anotherDivision)->create();
        Team::factory()->hasAttached($anotherDivision)->create();
        Team::factory()->hasAttached($anotherDivision)->create();

        $divisionTeams = $division->getTeams();

        $this->assertCount(5, $divisionTeams);
        $teams->each(
            function (Team $team) use ($divisionTeams): void {
                $this->assertContains($team->getId(), $divisionTeams->pluck('id'));
            }
        );
    }

    public function testItGetsTheFixtures(): void
    {
        /** @var Division $division */
        $division = Division::factory()->create();
        $fixtures = collect(
            [
                Fixture::factory()->inDivision($division)->create(),
                Fixture::factory()->inDivision($division)->create(),
                Fixture::factory()->inDivision($division)->create(),
                Fixture::factory()->inDivision($division)->create(),
            ]
        );

        $anotherDivision = Division::factory()->create();
        Team::factory()->hasAttached($anotherDivision)->create();
        Team::factory()->hasAttached($anotherDivision)->create();
        Team::factory()->hasAttached($anotherDivision)->create();

        $divisionFixtures = $division->getFixtures();

        $this->assertCount(4, $divisionFixtures);
        $fixtures->each(
            function (Fixture $fixture) use ($divisionFixtures): void {
                $this->assertContains($fixture->getId(), $divisionFixtures->pluck('id'));
            }
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        // No need to create roles every time we create a model
        Event::fake();
    }
}
