<?php

namespace Tests\Unit\Models;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class DivisionTest extends TestCase
{
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

    public function testItGetsTheNameOfTheAdminRole(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $this->assertEquals("Division {$division->getId()} Administrator", RolesHelper::divisionAdmin($division));
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

    public function testItGetsTheTeams(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $teams = collect([
            aTeam()->inDivision($division)->build(),
            aTeam()->inDivision($division)->build(),
            aTeam()->inDivision($division)->build(),
            aTeam()->inDivision($division)->build(),
            aTeam()->inDivision($division)->build(),
        ]);

        // Other teams
        $anotherDivision = factory(Division::class)->create();
        aTeam()->inDivision($anotherDivision)->build();
        aTeam()->inDivision($anotherDivision)->build();
        aTeam()->inDivision($anotherDivision)->build();

        $divisionTeams = $division->getTeams();

        $this->assertCount(5, $divisionTeams);
        $teams->each(function (Team $team) use ($divisionTeams): void {
            $this->assertContains($team->getId(), $divisionTeams->pluck('id'));
        });
    }

    public function testItGetsTheFixtures(): void
    {
        $division = factory(Division::class)->create();
        $fixtures = collect([
            aFixture()->inDivision($division)->build(),
            aFixture()->inDivision($division)->build(),
            aFixture()->inDivision($division)->build(),
            aFixture()->inDivision($division)->build(),
        ]);

        // Other teams
        $anotherDivision = factory(Division::class)->create();
        aTeam()->inDivision($anotherDivision)->build();
        aTeam()->inDivision($anotherDivision)->build();
        aTeam()->inDivision($anotherDivision)->build();

        /** @var Collection $divisionFixtures */
        $divisionFixtures = $division->getFixtures();

        $this->assertCount(4, $divisionFixtures);
        $fixtures->each(function (Fixture $fixture) use ($divisionFixtures): void {
            $this->assertContains($fixture->getId(), $divisionFixtures->pluck('id'));
        });
    }
}
