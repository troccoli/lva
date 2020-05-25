<?php

namespace Tests\Unit\Models;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpParser\Node\Expr\AssignOp\Div;
use Tests\TestCase;

class CompetitionTest extends TestCase
{
    use RefreshDatabase;

    public function testItGetsTheId(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $this->assertEquals($competition->id, $competition->getId());
    }

    public function testItGetsTheName(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $this->assertEquals($competition->name, $competition->getName());
    }

    public function testItGetsTheNameOfTheAdminRole(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $this->assertEquals("Competition {$competition->getId()} Administrator", $competition->getAdminRole());
    }

    public function testItGetsTheSeason(): void
    {
        $season = factory(Season::class)->create();
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create(['season_id' => $season->getId()]);
        $this->assertEquals($season->getId(), $competition->getSeason()->getId());
    }

    public function testItGetsTheDivisions(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $divisions = factory(Division::class)->times(3)->create(['competition_id' => $competition->getId()]);
        $competition2 = factory(Competition::class)->create();
        factory(Division::class)->times(7)->create(['competition_id' => $competition2->getId()]);

        $this->assertCount(3, $competition->getDivisions());
        $divisions->each(function (Division $division) use ($competition): void {
            $this->assertTrue($competition->getDivisions()->contains($division));
        });
    }

    public function testItGetsTheFixtures(): void
    {
        $competition = factory(Competition::class)->create();
        $division1 = factory(Division::class)->create(['competition_id' => $competition->getId()]);
        $division2 = factory(Division::class)->create(['competition_id' => $competition->getId()]);
        $division3 = factory(Division::class)->create(['competition_id' => $competition->getId()]);
        $fixtures = collect([
            aFixture()->inDivision($division1)->build(),
            aFixture()->inDivision($division1)->build(),
            aFixture()->inDivision($division2)->build(),
            aFixture()->inDivision($division3)->build(),
            aFixture()->inDivision($division3)->build(),
        ]);

        // Other fixtures
        $anotherDivision = factory(Division::class)->create();
        aFixture()->inDivision($anotherDivision)->build();
        aFixture()->inDivision($anotherDivision)->build();
        aFixture()->inDivision($anotherDivision)->build();

        /** @var Collection $competitionFixtures */
        $competitionFixtures = $competition->getFixtures();

        $this->assertCount(5, $competitionFixtures);
        $fixtures->each(function (Fixture $fixture) use ($competitionFixtures): void {
            $this->assertContains($fixture->getId(), $competitionFixtures->pluck('id'));
        });
    }
}
