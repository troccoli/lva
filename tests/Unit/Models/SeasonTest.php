<?php

namespace Tests\Unit;

use App\Models\Season;
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
}
