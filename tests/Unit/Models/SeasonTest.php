<?php

namespace Tests\Unit\Models;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SeasonTest extends TestCase
{
    public function testItGetsTheId(): void
    {
        $season = Season::factory()->create();
        $this->assertEquals($season->id, $season->getId());
    }

    public function testItGetsTheYear(): void
    {
        $season = Season::factory()->create();
        $this->assertEquals($season->year, $season->getYear());
    }

    public function testItGetsTheNameOfTheAdminRole(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create();
        $this->assertEquals("Season {$season->getId()} Administrator", RolesHelper::seasonAdmin($season));
    }

    /**
     * @dataProvider yearsProvider
     */
    public function testItGetsTheName(int $year, string $expectedName): void
    {
        $season = Season::factory()->create(['year' => $year]);
        $this->assertEquals($expectedName, $season->getName());
    }

    public function yearsProvider(): array
    {
        return [
            [1999, '1999/00'],
            [2000, '2000/01'],
            [2005, '2005/06'],
            [2009, '2009/10'],
            [2018, '2018/19'],
            [2099, '2099/00'],
        ];
    }

    public function testItGetsTheCompetitions(): void
    {
        $season = Season::factory()->create();
        $competitions = Competition::factory()->count(3)->create(['season_id' => $season->getId()]);
        $season2 = Season::factory()->create();
        Competition::factory()->count(7)->create(['season_id' => $season2->getId()]);

        $this->assertCount(3, $season->getCompetitions());
        $competitions->each(
            function (Competition $competition) use ($season): void {
                $this->assertTrue($season->getCompetitions()->contains($competition));
            }
        );
    }

    public function testItGetsTheFixtures(): void
    {
        $season = Season::factory()->create();
        $competition1 = Competition::factory()->create(['season_id' => $season->getId()]);
        $competition2 = Competition::factory()->create(['season_id' => $season->getId()]);
        /** @var Division $division1A */
        $division1A = Division::factory()->create(['competition_id' => $competition1->getId()]);
        /** @var Division $division2A */
        $division2A = Division::factory()->create(['competition_id' => $competition2->getId()]);
        /** @var Division $division2B */
        $division2B = Division::factory()->create(['competition_id' => $competition2->getId()]);
        $fixtures = collect(
            [
                Fixture::factory()->inDivision($division1A)->create(),
                Fixture::factory()->inDivision($division1A)->create(),
                Fixture::factory()->inDivision($division2A)->create(),
                Fixture::factory()->inDivision($division2B)->create(),
                Fixture::factory()->inDivision($division2B)->create(),
                Fixture::factory()->inDivision($division2B)->create(),
            ]
        );

        /** @var Division $anotherDivision */
        $anotherDivision = Division::factory()->create();
        Fixture::factory()->inDivision($anotherDivision)->create();
        Fixture::factory()->inDivision($anotherDivision)->create();
        Fixture::factory()->inDivision($anotherDivision)->create();

        $seasonFixtures = $season->getFixtures();

        $this->assertCount(6, $seasonFixtures);
        $fixtures->each(
            function (Fixture $fixture) use ($seasonFixtures): void {
                $this->assertContains($fixture->getId(), $seasonFixtures->pluck('id'));
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
