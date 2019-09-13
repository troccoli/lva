<?php

namespace Tests\Unit\Models;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeasonTest extends TestCase
{
    use RefreshDatabase;

    public function testItGetsTheId(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();
        $this->assertEquals($season->id, $season->getId());
    }

    public function testItGetsTheYear(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();
        $this->assertEquals($season->year, $season->getYear());
    }

    /**
     * @dataProvider yearsProvider
     */
    public function testItGetsTheName(int $year, string $expectedName): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create(['year' => $year]);
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
        /** @var Season $season */
        $season = factory(Season::class)->create();
        $competitions = factory(Competition::class)->times(3)->create(['season_id' => $season->getId()]);
        $season2 = factory(Season::class)->create();
        factory(Competition::class)->times(7)->create(['season_id' => $season2->getId()]);

        $this->assertCount(3, $season->getCompetitions());
        $competitions->each(function (Competition $competition) use ($season): void {
            $this->assertTrue($season->getCompetitions()->contains($competition));
        });
    }

    public function testItGetsTheFixtures(): void
    {
        $season = factory(Season::class)->create();
        $competition1 = factory(Competition::class)->create(['season_id' => $season->getId()]);
        $competition2 = factory(Competition::class)->create(['season_id' => $season->getId()]);
        $division1A = factory(Division::class)->create(['competition_id' => $competition1->getId()]);
        $division2A = factory(Division::class)->create(['competition_id' => $competition2->getId()]);
        $division2B = factory(Division::class)->create(['competition_id' => $competition2->getId()]);
        $fixtures = collect([
            aFixture()->inDivision($division1A)->build(),
            aFixture()->inDivision($division1A)->build(),
            aFixture()->inDivision($division2A)->build(),
            aFixture()->inDivision($division2B)->build(),
            aFixture()->inDivision($division2B)->build(),
            aFixture()->inDivision($division2B)->build(),
        ]);

        // Other fixtures
        $anotherDivision = factory(Division::class)->create();
        aFixture()->inDivision($anotherDivision)->build();
        aFixture()->inDivision($anotherDivision)->build();
        aFixture()->inDivision($anotherDivision)->build();

        /** @var Collection $seasonFixtures */
        $seasonFixtures = $season->getFixtures();

        $this->assertCount(6, $seasonFixtures);
        $fixtures->each(function (Fixture $fixture) use ($seasonFixtures): void {
            $this->assertContains($fixture->getId(), $seasonFixtures->pluck('id'));
        });
    }
}
