<?php

namespace Tests\Browser\CRUD;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\FixtureList;
use Tests\DuskTestCase;

class FixtureTest extends DuskTestCase
{
    /** @var Season */
    private $season1 = null;
    /** @var Season */
    private $season2 = null;
    /** @var Season */
    private $season3 = null;

    /** @var Competition */
    private $competition1_1 = null;
    /** @var Competition */
    private $competition2_1 = null;
    /** @var Competition */
    private $competition1_2 = null;
    /** @var Competition */
    private $competition2_2 = null;
    /** @var Competition */
    private $competition1_3 = null;
    /** @var Competition */
    private $competition2_3 = null;
    /** @var Competition */
    private $competition3_3 = null;
    /** @var Competition */
    private $competition4_3 = null;

    /** @var Division */
    private $division1_1_1 = null;
    /** @var Division */
    private $division2_1_1 = null;
    /** @var Division */
    private $division3_1_1 = null;
    /** @var Division */
    private $division1_2_1 = null;
    /** @var Division */
    private $division2_2_1 = null;
    /** @var Division */
    private $division3_2_1 = null;
    /** @var Division */
    private $division1_1_2 = null;
    /** @var Division */
    private $division2_1_2 = null;
    /** @var Division */
    private $division3_1_2 = null;
    /** @var Division */
    private $division1_2_2 = null;
    /** @var Division */
    private $division2_2_2 = null;
    /** @var Division */
    private $division3_2_2 = null;
    /** @var Division */
    private $division1_1_3 = null;
    /** @var Division */
    private $division2_1_3 = null;
    /** @var Division */
    private $division3_1_3 = null;
    /** @var Division */
    private $division1_2_3 = null;
    /** @var Division */
    private $division2_2_3 = null;
    /** @var Division */
    private $division3_2_3 = null;
    /** @var Division */
    private $division1_3_3 = null;
    /** @var Division */
    private $division2_3_3 = null;
    /** @var Division */
    private $division3_3_3 = null;
    /** @var Division */
    private $division1_4_3 = null;
    /** @var Division */
    private $division2_4_3 = null;
    /** @var Division */
    private $division3_4_3 = null;

    /** @var Team */
    private $team1 = null;
    /** @var Team */
    private $team2 = null;
    /** @var Team */
    private $team3 = null;
    /** @var Team */
    private $team4 = null;
    /** @var Team */
    private $team5 = null;
    /** @var Team */
    private $team6 = null;
    /** @var Team */
    private $team7 = null;
    /** @var Team */
    private $team8 = null;
    /** @var Team */
    private $team9 = null;
    /** @var Team */
    private $team10 = null;

    /**
     * @throws \Throwable
     */
    public function testListingAllFixturesWhenThereAreNone(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/fixtures')
                    ->pause(1500)
                    ->assertSeeIn('@season-selector', 'No seasons')
                    ->assertSeeIn('@competition-selector', 'No competitions')
                    ->assertSeeIn('@division-selector', 'No divisions')
                    ->assertSeeIn('@list', 'There are no fixtures yet.');

            $season = Season::factory()->create(['year' => 2015]);
            $browser->visit('/fixtures')
                    ->pause(1500)
                    ->assertSeeIn('@season-selector', '2015/16')
                    ->assertSeeIn('@competition-selector', 'No competitions')
                    ->assertSeeIn('@division-selector', 'No divisions')
                    ->assertSeeIn('@list', 'There are no fixtures yet.');

            $competition = Competition::factory()->for($season)->create(['name' => 'COMP1']);
            $browser->visit('/fixtures')
                    ->pause(1500)
                    ->assertSeeIn('@season-selector', '2015/16')
                    ->assertSeeIn('@competition-selector', 'COMP1')
                    ->assertSeeIn('@division-selector', 'No divisions')
                    ->assertSeeIn('@list', 'There are no fixtures yet.');

            $division = Division::factory()->for($competition)->create(['name' => 'DIV1', 'display_order' => 1]);
            $browser->visit('/fixtures')
                    ->pause(1500)
                    ->assertSeeIn('@season-selector', '2015/16')
                    ->assertSeeIn('@competition-selector', 'COMP1')
                    ->assertSeeIn('@division-selector', 'DIV1')
                    ->assertSeeIn('@list', 'There are no fixtures yet.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListingAllFixtures(): void
    {
        $this->browse(function (Browser $browser): void {
            $this->createTestFixtures();
            $browser->loginAs($this->siteAdmin);

            $browser->visit(new FixtureList())
                    ->pause(1500)
                    ->assertAttribute('@add', 'aria-label', 'Add a fixture')
                    ->assertButtonDisabled('@previousPage')
                    ->assertButtonEnabled('@nextPage');

            // Default season, competition and division - page 1
            $browser->assertSeeIn('@season-selector', '2019/20')
                    ->assertSeeIn('@competition-selector', 'S3C1')
                    ->assertSeeIn('@division-selector', 'S3C1D1')
                    ->pause(1500)
                    ->with('@list', function (Browser $table): void {
                        $this->assertTableHeader($table, ['Division', 'Home team', 'Away team']);

                        $this->assertTableRow($table, 1, ['S3C1D1', 'TEAM 1', 'TEAM 2']);
                        $this->assertTableRow($table, 2, ['S3C1D1', 'TEAM 3', 'TEAM 4']);
                        $this->assertTableRow($table, 3, ['S3C1D1', 'TEAM 1', 'TEAM 3']);
                        $this->assertTableRow($table, 4, ['S3C1D1', 'TEAM 2', 'TEAM 5']);
                        $this->assertTableRow($table, 5, ['S3C1D1', 'TEAM 1', 'TEAM 5']);
                        $this->assertTableRow($table, 6, ['S3C1D1', 'TEAM 2', 'TEAM 4']);
                        $this->assertTableRow($table, 7, ['S3C1D1', 'TEAM 1', 'TEAM 4']);
                        $this->assertTableRow($table, 8, ['S3C1D1', 'TEAM 3', 'TEAM 5']);
                        $this->assertTableRow($table, 9, ['S3C1D1', 'TEAM 2', 'TEAM 3']);
                        $this->assertTableRow($table, 10, ['S3C1D1', 'TEAM 4', 'TEAM 5']);
                    })
                // Default season, competition and division - page 2
                    ->press('@nextPage')
                    ->pause(1500)
                    ->with('@list', function (Browser $table): void {
                        $this->assertTableHeader($table, ['Division', 'Home team', 'Away team']);

                        $this->assertTableRow($table, 1, ['S3C1D1', 'TEAM 2', 'TEAM 1']);
                        $this->assertTableRow($table, 2, ['S3C1D1', 'TEAM 4', 'TEAM 3']);
                        $this->assertTableRow($table, 3, ['S3C1D1', 'TEAM 3', 'TEAM 1']);
                        $this->assertTableRow($table, 4, ['S3C1D1', 'TEAM 5', 'TEAM 2']);
                        $this->assertTableRow($table, 5, ['S3C1D1', 'TEAM 5', 'TEAM 1']);
                        $this->assertTableRow($table, 6, ['S3C1D1', 'TEAM 4', 'TEAM 2']);
                        $this->assertTableRow($table, 7, ['S3C1D1', 'TEAM 4', 'TEAM 1']);
                        $this->assertTableRow($table, 8, ['S3C1D1', 'TEAM 5', 'TEAM 3']);
                        $this->assertTableRow($table, 9, ['S3C1D1', 'TEAM 3', 'TEAM 2']);
                        $this->assertTableRow($table, 10, ['S3C1D1', 'TEAM 5', 'TEAM 4']);
                    });

            // Select a season, with default competition and division - page 1
            $browser->vuetifySelect('@season-selector', '2017/18')
                    ->pause(1500)
                    ->assertSeeIn('@season-selector', '2017/18')
                    ->assertSeeIn('@competition-selector', 'S2C1')
                    ->assertSeeIn('@division-selector', 'S2C1D1')
                    ->with('@list', function (Browser $table): void {
                        $this->assertTableHeader($table, ['Division', 'Home team', 'Away team']);

                        $this->assertTableRow($table, 1, ['S2C1D1', 'TEAM 4', 'TEAM 8']);
                        $this->assertTableRow($table, 2, ['S2C1D1', 'TEAM 4', 'TEAM 10']);
                        $this->assertTableRow($table, 3, ['S2C1D1', 'TEAM 8', 'TEAM 10']);
                        $this->assertTableRow($table, 4, ['S2C1D1', 'TEAM 8', 'TEAM 4']);
                        $this->assertTableRow($table, 5, ['S2C1D1', 'TEAM 10', 'TEAM 4']);
                        $this->assertTableRow($table, 6, ['S2C1D1', 'TEAM 10', 'TEAM 8']);
                    });

            // Select a season and a competition, default division - page 1
            $browser->vuetifySelect('@season-selector', '2019/20')
                    ->pause(1500)
                    ->assertSeeIn('@season-selector', '2019/20')
                    ->vuetifySelect('@competition-selector', 'S3C2')
                    ->pause(1500)
                    ->assertSeeIn('@competition-selector', 'S3C2')
                    ->assertSeeIn('@division-selector', 'S3C2D1')
                    ->with('@list', function (Browser $table): void {
                        $this->assertTableHeader($table, ['Division', 'Home team', 'Away team']);

                        $this->assertTableRow($table, 1, ['S3C2D1', 'TEAM 2', 'TEAM 4']);
                        $this->assertTableRow($table, 2, ['S3C2D1', 'TEAM 6', 'TEAM 8']);
                        $this->assertTableRow($table, 3, ['S3C2D1', 'TEAM 2', 'TEAM 6']);
                        $this->assertTableRow($table, 4, ['S3C2D1', 'TEAM 4', 'TEAM 10']);
                        $this->assertTableRow($table, 5, ['S3C2D1', 'TEAM 2', 'TEAM 10']);
                        $this->assertTableRow($table, 6, ['S3C2D1', 'TEAM 4', 'TEAM 8']);
                        $this->assertTableRow($table, 7, ['S3C2D1', 'TEAM 2', 'TEAM 8']);
                        $this->assertTableRow($table, 8, ['S3C2D1', 'TEAM 6', 'TEAM 10']);
                        $this->assertTableRow($table, 9, ['S3C2D1', 'TEAM 4', 'TEAM 6']);
                        $this->assertTableRow($table, 10, ['S3C2D1', 'TEAM 8', 'TEAM 10']);
                    });

            // Select a season, competition and division - page 1
            $browser->vuetifySelect('@season-selector', '2017/18')
                    ->pause(1500)
                    ->assertSeeIn('@season-selector', '2017/18')
                    ->vuetifySelect('@competition-selector', 'S2C2')
                    ->pause(1500)
                    ->assertSeeIn('@competition-selector', 'S2C2')
                    ->vuetifySelect('@division-selector', 'S2C2D3')
                    ->pause(1500)
                    ->assertSeeIn('@division-selector', 'S2C2D3')
                    ->with('@list', function (Browser $table): void {
                        $this->assertTableHeader($table, ['Division', 'Home team', 'Away team']);

                        $this->assertTableRow($table, 1, ['S2C2D3', 'TEAM 2', 'TEAM 3']);
                        $this->assertTableRow($table, 2, ['S2C2D3', 'TEAM 2', 'TEAM 5']);
                        $this->assertTableRow($table, 3, ['S2C2D3', 'TEAM 3', 'TEAM 5']);
                        $this->assertTableRow($table, 4, ['S2C2D3', 'TEAM 3', 'TEAM 2']);
                        $this->assertTableRow($table, 5, ['S2C2D3', 'TEAM 5', 'TEAM 2']);
                        $this->assertTableRow($table, 6, ['S2C2D3', 'TEAM 5', 'TEAM 3']);
                    });

            // Select a season, competition and division and go through pages
            $browser->vuetifySelect('@season-selector', '2019/20')
                    ->pause(1500)
                    ->assertSeeIn('@season-selector', '2019/20')
                    ->assertSeeIn('@competition-selector', 'S3C1')
                    ->vuetifySelect('@competition-selector', 'S3C4')
                    ->pause(1500)
                    ->assertSeeIn('@competition-selector', 'S3C4')
                    ->assertSeeIn('@division-selector', 'S3C4D1')
                // go to the second page
                    ->press('@nextPage')
                    ->pause(1500)
                    ->with('@list', function (Browser $table): void {
                        $this->assertTableHeader($table, ['Division', 'Home team', 'Away team']);

                        $this->assertTableRow($table, 1, ['S3C4D1', 'TEAM 9', 'TEAM 5']);
                        $this->assertTableRow($table, 2, ['S3C4D1', 'TEAM 2', 'TEAM 3']);
                        $this->assertTableRow($table, 3, ['S3C4D1', 'TEAM 10', 'TEAM 6']);
                        $this->assertTableRow($table, 4, ['S3C4D1', 'TEAM 8', 'TEAM 7']);
                        $this->assertTableRow($table, 5, ['S3C4D1', 'TEAM 1', 'TEAM 4']);
                        $this->assertTableRow($table, 6, ['S3C4D1', 'TEAM 5', 'TEAM 2']);
                        $this->assertTableRow($table, 7, ['S3C4D1', 'TEAM 9', 'TEAM 10']);
                        $this->assertTableRow($table, 8, ['S3C4D1', 'TEAM 3', 'TEAM 8']);
                        $this->assertTableRow($table, 9, ['S3C4D1', 'TEAM 6', 'TEAM 1']);
                        $this->assertTableRow($table, 10, ['S3C4D1', 'TEAM 7', 'TEAM 4']);
                    })
                // go to the third page
                    ->press('@nextPage')
                    ->pause(1500)
                    ->with('@list', function (Browser $table): void {
                        $this->assertTableHeader($table, ['Division', 'Home team', 'Away team']);

                        $this->assertTableRow($table, 1, ['S3C4D1', 'TEAM 10', 'TEAM 5']);
                        $this->assertTableRow($table, 2, ['S3C4D1', 'TEAM 8', 'TEAM 2']);
                        $this->assertTableRow($table, 3, ['S3C4D1', 'TEAM 1', 'TEAM 9']);
                        $this->assertTableRow($table, 4, ['S3C4D1', 'TEAM 4', 'TEAM 3']);
                        $this->assertTableRow($table, 5, ['S3C4D1', 'TEAM 7', 'TEAM 6']);
                        $this->assertTableRow($table, 6, ['S3C4D1', 'TEAM 5', 'TEAM 8']);
                        $this->assertTableRow($table, 7, ['S3C4D1', 'TEAM 10', 'TEAM 1']);
                        $this->assertTableRow($table, 8, ['S3C4D1', 'TEAM 2', 'TEAM 4']);
                        $this->assertTableRow($table, 9, ['S3C4D1', 'TEAM 9', 'TEAM 7']);
                        $this->assertTableRow($table, 10, ['S3C4D1', 'TEAM 3', 'TEAM 6']);
                    })
                // go back to the second page
                    ->press('@previousPage')
                    ->pause(1500)
                    ->with('@list', function (Browser $table): void {
                        $this->assertTableHeader($table, ['Division', 'Home team', 'Away team']);

                        $this->assertTableRow($table, 1, ['S3C4D1', 'TEAM 9', 'TEAM 5']);
                        $this->assertTableRow($table, 2, ['S3C4D1', 'TEAM 2', 'TEAM 3']);
                        $this->assertTableRow($table, 3, ['S3C4D1', 'TEAM 10', 'TEAM 6']);
                        $this->assertTableRow($table, 4, ['S3C4D1', 'TEAM 8', 'TEAM 7']);
                        $this->assertTableRow($table, 5, ['S3C4D1', 'TEAM 1', 'TEAM 4']);
                        $this->assertTableRow($table, 6, ['S3C4D1', 'TEAM 5', 'TEAM 2']);
                        $this->assertTableRow($table, 7, ['S3C4D1', 'TEAM 9', 'TEAM 10']);
                        $this->assertTableRow($table, 8, ['S3C4D1', 'TEAM 3', 'TEAM 8']);
                        $this->assertTableRow($table, 9, ['S3C4D1', 'TEAM 6', 'TEAM 1']);
                        $this->assertTableRow($table, 10, ['S3C4D1', 'TEAM 7', 'TEAM 4']);
                    });
        });
    }

    private function assertTableHeader(Browser $table, array $headers): void
    {
        foreach ($headers as $column => $header) {
            $child = $column + 1;
            $table->assertSeeIn("thead tr:nth-child(1) th:nth-child($child)", $header);
        }
    }

    private function assertTableRow(Browser $table, int $rowNumber, array $values): void
    {
        foreach ($values as $column => $value) {
            $child = $column + 1;
            $table->assertSeeIn("tbody tr:nth-child($rowNumber) td:nth-child($child)", $value);
        }
    }

    private function createTestSeasons(): void
    {
        $this->season1 = Season::factory()->create(['year' => 2015]);
        $this->season2 = Season::factory()->create(['year' => 2017]);
        $this->season3 = Season::factory()->create(['year' => 2019]);
    }

    private function createTestCompetitions(): void
    {
        $this->createTestSeasons();

        $this->competition1_1 = Competition::factory()->for($this->season1)->create(['name' => 'S1C1']);
        $this->competition2_1 = Competition::factory()->for($this->season1)->create(['name' => 'S1C2']);
        $this->competition1_2 = Competition::factory()->for($this->season2)->create(['name' => 'S2C1']);
        $this->competition2_2 = Competition::factory()->for($this->season2)->create(['name' => 'S2C2']);
        $this->competition1_3 = Competition::factory()->for($this->season3)->create(['name' => 'S3C1']);
        $this->competition2_3 = Competition::factory()->for($this->season3)->create(['name' => 'S3C2']);
        $this->competition3_3 = Competition::factory()->for($this->season3)->create(['name' => 'S3C3']);
        $this->competition4_3 = Competition::factory()->for($this->season3)->create(['name' => 'S3C4']);
    }

    private function createTestDivisions(): void
    {
        $this->createTestCompetitions();

        $this->division1_1_1 = Division::factory()->for($this->competition1_1)->create([
            'name' => 'S1C1D1',
            'display_order' => 1,
        ]);
        $this->division2_1_1 = Division::factory()->for($this->competition1_1)->create([
            'name' => 'S1C1D2',
            'display_order' => 2,
        ]);
        $this->division3_1_1 = Division::factory()->for($this->competition1_1)->create([
            'name' => 'S1C1D3',
            'display_order' => 3,
        ]);
        $this->division1_2_1 = Division::factory()->for($this->competition2_1)->create([
            'name' => 'S1C2D1',
            'display_order' => 1,
        ]);
        $this->division2_2_1 = Division::factory()->for($this->competition2_1)->create([
            'name' => 'S1C2D2',
            'display_order' => 2,
        ]);
        $this->division3_2_1 = Division::factory()->for($this->competition2_1)->create([
            'name' => 'S1C2D3',
            'display_order' => 3,
        ]);
        $this->division1_1_2 = Division::factory()->for($this->competition1_2)->create([
            'name' => 'S2C1D1',
            'display_order' => 1,
        ]);
        $this->division2_1_2 = Division::factory()->for($this->competition1_2)->create([
            'name' => 'S2C1D2',
            'display_order' => 2,
        ]);
        $this->division3_1_2 = Division::factory()->for($this->competition1_2)->create([
            'name' => 'S2C1D3',
            'display_order' => 3,
        ]);
        $this->division1_2_2 = Division::factory()->for($this->competition2_2)->create([
            'name' => 'S2C2D1',
            'display_order' => 1,
        ]);
        $this->division2_2_2 = Division::factory()->for($this->competition2_2)->create([
            'name' => 'S2C2D2',
            'display_order' => 2,
        ]);
        $this->division3_2_2 = Division::factory()->for($this->competition2_2)->create([
            'name' => 'S2C2D3',
            'display_order' => 3,
        ]);
        $this->division1_1_3 = Division::factory()->for($this->competition1_3)->create([
            'name' => 'S3C1D1',
            'display_order' => 1,
        ]);
        $this->division2_1_3 = Division::factory()->for($this->competition1_3)->create([
            'name' => 'S3C1D2',
            'display_order' => 2,
        ]);
        $this->division3_1_3 = Division::factory()->for($this->competition1_3)->create([
            'name' => 'S3C1D3',
            'display_order' => 3,
        ]);
        $this->division1_2_3 = Division::factory()->for($this->competition2_3)->create([
            'name' => 'S3C2D1',
            'display_order' => 1,
        ]);
        $this->division2_2_3 = Division::factory()->for($this->competition2_3)->create([
            'name' => 'S3C2D2',
            'display_order' => 2,
        ]);
        $this->division3_2_3 = Division::factory()->for($this->competition2_3)->create([
            'name' => 'S3C2D3',
            'display_order' => 3,
        ]);
        $this->division1_3_3 = Division::factory()->for($this->competition3_3)->create([
            'name' => 'S3C3D1',
            'display_order' => 1,
        ]);
        $this->division2_3_3 = Division::factory()->for($this->competition3_3)->create([
            'name' => 'S3C3D2',
            'display_order' => 2,
        ]);
        $this->division3_3_3 = Division::factory()->for($this->competition3_3)->create([
            'name' => 'S3C3D3',
            'display_order' => 3,
        ]);
        $this->division1_4_3 = Division::factory()->for($this->competition4_3)->create([
            'name' => 'S3C4D1',
            'display_order' => 1,
        ]);
        $this->division2_4_3 = Division::factory()->for($this->competition4_3)->create([
            'name' => 'S3C4D2',
            'display_order' => 2,
        ]);
        $this->division3_4_3 = Division::factory()->for($this->competition4_3)->create([
            'name' => 'S3C4D3',
            'display_order' => 3,
        ]);
    }

    private function createTestClubs(): void
    {
    }

    private function createTestTeams(): void
    {
        $this->createTestClubs();

        $this->team1 = Team::factory()->create(['name' => 'TEAM 1']);
        $this->team2 = Team::factory()->create(['name' => 'TEAM 2']);
        $this->team3 = Team::factory()->create(['name' => 'TEAM 3']);
        $this->team4 = Team::factory()->create(['name' => 'TEAM 4']);
        $this->team5 = Team::factory()->create(['name' => 'TEAM 5']);
        $this->team6 = Team::factory()->create(['name' => 'TEAM 6']);
        $this->team7 = Team::factory()->create(['name' => 'TEAM 7']);
        $this->team8 = Team::factory()->create(['name' => 'TEAM 8']);
        $this->team9 = Team::factory()->create(['name' => 'TEAM 9']);
        $this->team10 = Team::factory()->create(['name' => 'TEAM 10']);
    }

    private function populateTestDivisions(): void
    {
        $this->createTestDivisions();
        $this->createTestTeams();

        // D1, C1, S1 => T1, T2, T3
        $this->team1->divisions()->attach($this->division1_1_1->getId());
        $this->team2->divisions()->attach($this->division1_1_1->getId());
        $this->team3->divisions()->attach($this->division1_1_1->getId());

        // D2, C1, S1 => T4, T5, T6
        $this->team4->divisions()->attach($this->division2_1_1->getId());
        $this->team5->divisions()->attach($this->division2_1_1->getId());
        $this->team6->divisions()->attach($this->division2_1_1->getId());

        // D3, C1, S1 => T7, T8, T9, T10
        $this->team7->divisions()->attach($this->division3_1_1->getId());
        $this->team8->divisions()->attach($this->division3_1_1->getId());
        $this->team9->divisions()->attach($this->division3_1_1->getId());
        $this->team10->divisions()->attach($this->division3_1_1->getId());

        // D1, C2, S1 => T1, T2, T3
        $this->team1->divisions()->attach($this->division1_2_1->getId());
        $this->team2->divisions()->attach($this->division1_2_1->getId());
        $this->team3->divisions()->attach($this->division1_2_1->getId());

        // D2, C2, S1 => T4, T5, T6
        $this->team4->divisions()->attach($this->division2_2_1->getId());
        $this->team5->divisions()->attach($this->division2_2_1->getId());
        $this->team6->divisions()->attach($this->division2_2_1->getId());

        // D3, C2, S1 => T7, T8, T9, T10
        $this->team7->divisions()->attach($this->division3_2_1->getId());
        $this->team8->divisions()->attach($this->division3_2_1->getId());
        $this->team9->divisions()->attach($this->division3_2_1->getId());
        $this->team10->divisions()->attach($this->division3_2_1->getId());

        // D1, C1, S2 => T4, T8, T10
        $this->team4->divisions()->attach($this->division1_1_2->getId());
        $this->team8->divisions()->attach($this->division1_1_2->getId());
        $this->team10->divisions()->attach($this->division1_1_2->getId());

        // D2, C1, S2 => T1, T6, T7, T9
        $this->team1->divisions()->attach($this->division2_1_2->getId());
        $this->team6->divisions()->attach($this->division2_1_2->getId());
        $this->team7->divisions()->attach($this->division2_1_2->getId());
        $this->team9->divisions()->attach($this->division2_1_2->getId());

        // D3, C1, S2 => T2, T3, T5
        $this->team2->divisions()->attach($this->division3_1_2->getId());
        $this->team3->divisions()->attach($this->division3_1_2->getId());
        $this->team5->divisions()->attach($this->division3_1_2->getId());

        // D1, C2, S2 => T4, T8, T10
        $this->team4->divisions()->attach($this->division1_2_2->getId());
        $this->team8->divisions()->attach($this->division1_2_2->getId());
        $this->team10->divisions()->attach($this->division1_2_2->getId());

        // D2, C2, S2 => T1, T6, T7, T9
        $this->team1->divisions()->attach($this->division2_2_2->getId());
        $this->team6->divisions()->attach($this->division2_2_2->getId());
        $this->team7->divisions()->attach($this->division2_2_2->getId());
        $this->team9->divisions()->attach($this->division2_2_2->getId());

        // D3, C2, S2 => T2, T3, T5
        $this->team2->divisions()->attach($this->division3_2_2->getId());
        $this->team3->divisions()->attach($this->division3_2_2->getId());
        $this->team5->divisions()->attach($this->division3_2_2->getId());

        // D1, C1, S3 => T1, T2, T3, T4, T5
        $this->team1->divisions()->attach($this->division1_1_3->getId());
        $this->team2->divisions()->attach($this->division1_1_3->getId());
        $this->team3->divisions()->attach($this->division1_1_3->getId());
        $this->team4->divisions()->attach($this->division1_1_3->getId());
        $this->team5->divisions()->attach($this->division1_1_3->getId());

        // D2, C1, S3 => T6, T7, T8, T9, T10
        $this->team6->divisions()->attach($this->division2_1_3->getId());
        $this->team7->divisions()->attach($this->division2_1_3->getId());
        $this->team8->divisions()->attach($this->division2_1_3->getId());
        $this->team9->divisions()->attach($this->division2_1_3->getId());
        $this->team10->divisions()->attach($this->division2_1_3->getId());

        // D3, C1, S3 =>
        // No teams

        // D1, C2, S3 => T2, T4, T6, T8, T10
        $this->team2->divisions()->attach($this->division1_2_3->getId());
        $this->team4->divisions()->attach($this->division1_2_3->getId());
        $this->team6->divisions()->attach($this->division1_2_3->getId());
        $this->team8->divisions()->attach($this->division1_2_3->getId());
        $this->team10->divisions()->attach($this->division1_2_3->getId());

        // D2, C2, S3 =>
        // No teams

        // D3, C2, S3 => T1, T3, T5, T7, T9
        $this->team1->divisions()->attach($this->division3_2_3->getId());
        $this->team3->divisions()->attach($this->division3_2_3->getId());
        $this->team5->divisions()->attach($this->division3_2_3->getId());
        $this->team7->divisions()->attach($this->division3_2_3->getId());
        $this->team9->divisions()->attach($this->division3_2_3->getId());

        // D1, C3, S3 => T1, T4, T7, T10
        $this->team1->divisions()->attach($this->division1_3_3->getId());
        $this->team4->divisions()->attach($this->division1_3_3->getId());
        $this->team7->divisions()->attach($this->division1_3_3->getId());
        $this->team10->divisions()->attach($this->division1_3_3->getId());

        // D2, C3, S3 => T2, T5, T8
        $this->team2->divisions()->attach($this->division2_3_3->getId());
        $this->team5->divisions()->attach($this->division2_3_3->getId());
        $this->team8->divisions()->attach($this->division2_3_3->getId());

        // D3, C3, S3 => T3, T6, T9
        $this->team3->divisions()->attach($this->division3_3_3->getId());
        $this->team6->divisions()->attach($this->division3_3_3->getId());
        $this->team9->divisions()->attach($this->division3_3_3->getId());

        // D1, C4, S3 => T1, T2, T3, T4, T5, T6, T7, T8, T9, T10
        $this->team1->divisions()->attach($this->division1_4_3->getId());
        $this->team2->divisions()->attach($this->division1_4_3->getId());
        $this->team3->divisions()->attach($this->division1_4_3->getId());
        $this->team4->divisions()->attach($this->division1_4_3->getId());
        $this->team5->divisions()->attach($this->division1_4_3->getId());
        $this->team6->divisions()->attach($this->division1_4_3->getId());
        $this->team7->divisions()->attach($this->division1_4_3->getId());
        $this->team8->divisions()->attach($this->division1_4_3->getId());
        $this->team9->divisions()->attach($this->division1_4_3->getId());
        $this->team10->divisions()->attach($this->division1_4_3->getId());

        // D2, C4, S3 =>
        // No teams

        // D3, C4, S3 =>
        // No teams
    }

    private function createTestFixtures(): void
    {
        $this->populateTestDivisions();

        // D1, C1, S1 => T1, T2, T3
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2015-05-14 19:30');
        Fixture::factory()
               ->inDivision($this->division1_1_1)
               ->between($this->team1, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_1)
               ->between($this->team1, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_1)
               ->between($this->team2, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_1)
               ->between($this->team2, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_1)
               ->between($this->team3, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_1)
               ->between($this->team3, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D2, C1, S1 => T4, T5, T6
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2015-05-14 19:30');
        Fixture::factory()
               ->inDivision($this->division2_1_1)
               ->between($this->team4, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_1)
               ->between($this->team4, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_1)
               ->between($this->team5, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_1)
               ->between($this->team5, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_1)
               ->between($this->team6, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_1)
               ->between($this->team6, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D3, C1, S1 => T7, T8, T9, T10
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2015-05-14 19:30');
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team7, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team9, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team7, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team8, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team7, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team8, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team8, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team10, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team9, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team10, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team10, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_1_1)
               ->between($this->team9, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D1, C2, S1 => T1, T2, T3
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2015-07-01 19:30');
        Fixture::factory()
               ->inDivision($this->division1_2_1)
               ->between($this->team1, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_1)
               ->between($this->team1, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_1)
               ->between($this->team2, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_1)
               ->between($this->team2, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_1)
               ->between($this->team3, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_1)
               ->between($this->team3, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D2, C2, S1 => T4, T5, T6
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2015-07-01 19:30');
        Fixture::factory()
               ->inDivision($this->division2_2_1)
               ->between($this->team4, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_1)
               ->between($this->team4, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_1)
               ->between($this->team5, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_1)
               ->between($this->team5, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_1)
               ->between($this->team6, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_1)
               ->between($this->team6, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D3, C2, S1 => T7, T8, T9, T10
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2015-07-01 19:30');
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team7, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team9, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team7, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team8, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team7, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team8, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team8, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team10, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team9, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team10, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team10, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_1)
               ->between($this->team9, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D1, C1, S2 => T4, T8, T10
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2017-11-21 10:00');
        Fixture::factory()
               ->inDivision($this->division1_1_2)
               ->between($this->team4, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_2)
               ->between($this->team4, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_2)
               ->between($this->team8, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_2)
               ->between($this->team8, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_2)
               ->between($this->team10, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_2)
               ->between($this->team10, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D2, C1, S2 => T1, T6, T7, T9
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2017-04-21 10:00');
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team1, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team7, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team1, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team6, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team1, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team6, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team6, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team9, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team7, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team9, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team9, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_2)
               ->between($this->team7, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D3, C1, S2 => T2, T3, T5
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2017-04-21 10:00');
        Fixture::factory()
               ->inDivision($this->division3_1_2)
               ->between($this->team2, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_2)
               ->between($this->team2, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_2)
               ->between($this->team3, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_2)
               ->between($this->team3, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_2)
               ->between($this->team5, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_1_2)
               ->between($this->team5, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D1, C2, S2 => T4, T8, T10
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2017-11-06 11:00');
        Fixture::factory()
               ->inDivision($this->division1_2_2)
               ->between($this->team4, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_2)
               ->between($this->team4, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_2)
               ->between($this->team8, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_2)
               ->between($this->team8, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_2)
               ->between($this->team10, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_2)
               ->between($this->team10, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D2, C2, S2 => T1, T6, T7, T9
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2017-11-06 11:00');
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team1, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team7, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team1, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team6, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team1, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team6, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team6, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team9, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team7, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team9, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team9, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_2_2)
               ->between($this->team7, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D3, C2, S2 => T2, T3, T5
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2017-11-06 11:00');
        Fixture::factory()
               ->inDivision($this->division3_2_2)
               ->between($this->team2, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_2)
               ->between($this->team2, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_2)
               ->between($this->team3, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_2)
               ->between($this->team3, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_2)
               ->between($this->team5, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_2)
               ->between($this->team5, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D1, C1, S3 => T1, T2, T3, T4, T5
        $matchNumber = 1;
        // Day 1 - no team5
        $date = Carbon::parse('2019-09-22 17:00');
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team1, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team3, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2 - no team4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team1, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team2, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3 - no team3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team1, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team2, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4 - no team2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team1, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team3, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5 - no team1
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team2, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team4, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6 - no team5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team2, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team4, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 7 - no team4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team3, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team5, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 8 - no team3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team5, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team4, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 9 - no team2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team4, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team5, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 10 - no team1
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team3, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_1_3)
               ->between($this->team5, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D2, C1, S3 => T6, T7, T8, T9, T10
        $matchNumber = 1;
        // Day 1 - no team10
        $date = Carbon::parse('2019-09-22 17:00');
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team6, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team8, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2 - no team9
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team6, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team7, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3 - no team8
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team6, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team7, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4 - no team7
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team6, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team8, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5 - no team6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team7, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team9, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6 - no team10
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team7, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team9, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 7 - no team9
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team8, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team10, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 8 - no team8
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team10, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team9, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 9 - no team7
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team9, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team10, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 0 - no team6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team8, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division2_1_3)
               ->between($this->team10, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D3, C1, S3 =>
        // Nothing to do here

        // D1, C2, S3 => T2, T4, T6, T8, T10
        $matchNumber = 1;
        // Day 1 - no team10
        $date = Carbon::parse('2019-06-12 12:30');
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team2, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team6, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2 - no team8
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team2, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team4, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3 - no team6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team2, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team4, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4 - no team4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team2, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team6, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5 - no team2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team4, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team8, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6 - no team10
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team4, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team8, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 7 - no team8
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team6, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team10, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 8 - no team6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team10, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team8, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 9 - no team4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team8, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team10, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 10 - no team2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team6, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_2_3)
               ->between($this->team10, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D2, C2, S3 =>
        // Nothing to do here

        // D3, C2, S3 => T1, T3, T5, T7, T9
        $matchNumber = 1;
        // Day 1 - no team9
        $date = Carbon::parse('2019-06-12 12:30');
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team1, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team5, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2 - no team7
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team1, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team3, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3 - no team5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team1, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team3, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4 - no team3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team1, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team5, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5 - no team1
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team3, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team7, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6 - no team9
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team3, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team7, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 7 - no team7
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team5, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team9, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 8 - no team5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team9, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team7, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 9 - no team3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team7, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team9, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 10 - no team1
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team5, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division3_2_3)
               ->between($this->team9, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D1, C3, S3 => T1, T4, T7, T10
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2019-02-18 12:00');
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team1, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team7, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team1, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team4, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team1, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team4, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team4, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team10, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team7, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team10, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team10, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_3_3)
               ->between($this->team7, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D2, C3, S3 => T2, T5, T8
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2019-02-18 12:00');
        Fixture::factory()
               ->inDivision($this->division2_3_3)
               ->between($this->team2, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_3_3)
               ->between($this->team5, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_3_3)
               ->between($this->team2, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_3_3)
               ->between($this->team5, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_3_3)
               ->between($this->team8, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division2_3_3)
               ->between($this->team8, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D3, C3, S3 => T3, T6, T9
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2019-02-18 12:00');
        Fixture::factory()
               ->inDivision($this->division3_3_3)
               ->between($this->team3, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_3_3)
               ->between($this->team3, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_3_3)
               ->between($this->team6, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division3_3_3)
               ->between($this->team6, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();        // Day 5        $date->addDay();        Fixture::factory()->inDivision($this->division3_3_3)->between($this->team9, $this->team3)->on(            $date,            $date        )->number($matchNumber++)->create();        // Day 6        $date->addDay();        Fixture::factory()->inDivision($this->division3_3_3)->between($this->team9, $this->team6)->on(            $date,            $date        )->number($matchNumber++)->create();

        // D1, C4, S3 => T1, T2, T3, T4, T5, T6, T7, T8, T9, T10
        $matchNumber = 1;
        // Day 1
        $date = Carbon::parse('2019-04-10 15:45');
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team6, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team3, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team9, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team2, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team10, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 2
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team5, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team6, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team7, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team4, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team1, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 3
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team9, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team2, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team10, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team8, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team1, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 4
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team5, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team9, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team3, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team6, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team7, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 5
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team10, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team8, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team1, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team4, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team7, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 6
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team5, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team10, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team2, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team9, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team3, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 7
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team1, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team4, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team7, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team6, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team3, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 8
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team5, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team1, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team8, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team10, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team2, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 9
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team7, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team6, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team3, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team9, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team2, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 10
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team5, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team7, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team4, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team1, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team8, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 11
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team3, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team9, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team2, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team10, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team8, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 12
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team5, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team3, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team6, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team7, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team4, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 13
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team2, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team10, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team8, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team1, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team4, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 14
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team5, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team2, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team9, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team3, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team6, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 15
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team8, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team1, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team4, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team7, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team6, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 16
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team5, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team8, $this->team4)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team10, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team2, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team9, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 17
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team4, $this->team5)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team7, $this->team1)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team6, $this->team8)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team3, $this->team10)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team9, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        // Day 18
        $date->addDay();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team5, $this->team7)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team4, $this->team6)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team1, $this->team3)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team8, $this->team9)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();
        Fixture::factory()
               ->inDivision($this->division1_4_3)
               ->between($this->team10, $this->team2)
               ->on($date, $date)
               ->number($matchNumber++)
               ->create();

        // D2, C4, S3 =>
        // Nothing to do here

        // D3, C4, S3 =>
        // Nothing to do here
    }
}
