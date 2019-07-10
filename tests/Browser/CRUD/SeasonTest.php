<?php

namespace Tests\Browser\CRUD;

use App\Models\Competition;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use App\Models\Season;
use App\Models\User;
use Tests\DuskTestCase;

class SeasonTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListSeasons(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit('/seasons')
                ->assertSeeIn('@list', 'There are no seasons yet.');

            /** @var Collection $seasons */
            $seasons = factory(Season::class)->times(7)->create()->sortByDesc('year');

            $page1 = $seasons->slice(0, 5);
            $page2 = $seasons->slice(5, 2);

            $browser->visit('/seasons')
                ->assertSeeLink('New season')
                ->with('@list', function (Browser $table) use ($page1): void {
                    $child = 1;
                    foreach ($page1 as $season) {
                        /** @var Season $season */
                        $table->with("tr:nth-child($child)", function (Browser $row) use ($season): void {
                            $row->assertSeeIn('td:nth-child(1)', $season->getName());
                        });
                        $child++;
                    }
                })
                ->with('div.pagination', function (Browser $nav): void {
                    $nav->clickLink(2);
                })
                ->assertPathIs('/seasons')
                ->assertQueryStringHas('page', 2)
                ->with('@list', function (Browser $table) use ($page2): void {
                    $child = 1;
                    foreach ($page2 as $season) {
                        /** @var Season $season */
                        $table->with("tr:nth-child($child)", function (Browser $row) use ($season): void {
                            $row->assertSeeIn('td:nth-child(1)', $season->getName());
                        });
                        $child++;
                    }
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            // Check we can add a season from the landing page
            $browser->visit('/seasons')
                ->clickLink('New season')
                ->assertPathIs('/seasons/create');

            // Check the form
            $browser->visit('/seasons/create')
                ->assertInputValue('year', '')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'ADD SEASON');

            // All fields missing
            $browser->visit('/seasons/create')
                ->type('year', ' ')// This is to get around the HTML5 validation on the browser
                ->press('ADD SEASON')
                ->assertPathIs('/seasons/create')
                ->assertSeeIn('@year-error', 'The year is required.');

            /** @var Season $season */
            $season = factory(Season::class)->make();
            // Brand new season
            $browser->visit('/seasons/create')
                ->type('year', $season->getYear())
                ->press('ADD SEASON')
                ->assertPathIs('/seasons')
                ->assertSee('Season added!');

            // Add the same season
            $browser->visit('/seasons/create')
                ->type('year', $season->getYear())
                ->press('ADD SEASON')
                ->assertPathIs('/seasons/create')
                ->assertSeeIn('@year-error', 'The season already exists.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Season $season */
            $season = factory(Season::class)->create();

            // Check we can edit a season from the landing page
            $browser->visit('/seasons')
                ->with('@list', function (Browser $table): void {
                    $table->clickLink('Update');
                })
                ->assertPathIs('/seasons/' . $season->getId() . '/edit');

            // Check the form
            $browser->visit('/seasons/' . $season->getId() . '/edit')
                ->assertInputValue('year', $season->getYear())
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // Don't change anything
            $browser->visit('/seasons/' . $season->getId() . '/edit')
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons')
                ->assertSee('Season updated!');

            /** @var Season $newSeason */
            $newSeason = factory(Season::class)->make();

            // Remove required fields
            $browser->visit('/seasons/' . $season->getId() . '/edit')
                ->type('year', ' ')// This is to get around the HTML5 validation on the browser
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons/' . $season->getId() . '/edit')
                ->assertSeeIn('@year-error', 'The year is required.');

            // Edit all details
            $browser->visit('/seasons/' . $season->getId() . '/edit')
                ->type('year', $newSeason->getYear())
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons')
                ->assertSee('Season updated!');

            $newSeason = factory(Season::class)->create();

            // Use an already existing season
            $browser->visit('/seasons/' . $season->getId() . '/edit')
                ->type('year', $newSeason->getYear())
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons/' . $season->getId() . '/edit')
                ->assertSeeIn('@year-error', 'The season already exists.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeleteSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Season $season */
            $season = factory(Season::class)->create();

            $browser->visit('/seasons')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Season deleted!')
                ->assertSee($season->getName());
            $browser->visit('/seasons')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Season deleted!')
                ->assertDontSee($season->getName());

            // Delete season with existing competition
            $season = factory(Season::class)->create();
            factory(Competition::class)->create(['season_id' => $season->getId()]);
            $browser->visit('/seasons')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Cannot delete because there are existing competitions in this season!')
                ->assertSeeIn('@list', $season->getName());
        });
    }

    /**
     * @throws \Throwable
     */
    public function testViewSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Season $season */
            $season = factory(Season::class)->create();

            factory(Competition::class)->times(2)->create(['season_id' => $season->getId()]);

            $browser->visit('/seasons')
                ->with('@list', function (Browser $table): void {
                    $table->clickLink('View');
                })
                ->assertPathIs('/competitions')
                ->assertQueryStringHas('season_id', $season->getId());
        });
    }
}
