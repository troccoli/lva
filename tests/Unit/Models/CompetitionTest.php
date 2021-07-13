<?php

namespace Tests\Unit\Models;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CompetitionTest extends TestCase
{
    public function testItGetsTheId(): void
    {
        $competition = Competition::factory()->create();
        $this->assertEquals($competition->id, $competition->getId());
    }

    public function testItGetsTheName(): void
    {
        $competition = Competition::factory()->create();
        $this->assertEquals($competition->name, $competition->getName());
    }

    public function testItGetsTheNameOfTheAdminRole(): void
    {
        /** @var Competition $competition */
        $competition = Competition::factory()->create();
        $this->assertEquals(
            "Competition {$competition->getId()} Administrator",
            RolesHelper::competitionAdmin($competition)
        );
    }

    public function testItGetsTheSeason(): void
    {
        $season = Season::factory()->create();
        $competition = Competition::factory()->create(['season_id' => $season->getId()]);
        $this->assertEquals($season->getId(), $competition->getSeason()->getId());
    }

    public function testItGetsTheDivisions(): void
    {
        $competition = Competition::factory()->create();
        $divisions = Division::factory()->count(3)->create(['competition_id' => $competition->getId()]);
        $competition2 = Competition::factory()->create();
        Division::factory()->count(7)->create(['competition_id' => $competition2->getId()]);

        $this->assertCount(3, $competition->getDivisions());
        $divisions->each(
            function (Division $division) use ($competition): void {
                $this->assertTrue($competition->getDivisions()->contains($division));
            }
        );
    }

    public function testItGetsTheFixtures(): void
    {
        $competition = Competition::factory()->create();
        /** @var Division $division1 */
        $division1 = Division::factory()->create(['competition_id' => $competition->getId()]);
        /** @var Division $division2 */
        $division2 = Division::factory()->create(['competition_id' => $competition->getId()]);
        /** @var Division $division3 */
        $division3 = Division::factory()->create(['competition_id' => $competition->getId()]);
        $fixtures = collect(
            [
                Fixture::factory()->inDivision($division1)->create(),
                Fixture::factory()->inDivision($division1)->create(),
                Fixture::factory()->inDivision($division2)->create(),
                Fixture::factory()->inDivision($division3)->create(),
                Fixture::factory()->inDivision($division3)->create(),
            ]
        );

        /** @var Division $anotherDivision */
        $anotherDivision = Division::factory()->create();
        Fixture::factory()->inDivision($anotherDivision)->create();
        Fixture::factory()->inDivision($anotherDivision)->create();
        Fixture::factory()->inDivision($anotherDivision)->create();

        $competitionFixtures = $competition->getFixtures();

        $this->assertCount(5, $competitionFixtures);
        $fixtures->each(
            function (Fixture $fixture) use ($competitionFixtures): void {
                $this->assertContains($fixture->getId(), $competitionFixtures->pluck('id'));
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
