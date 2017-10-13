<?php

namespace Tests\Browser\Admin\DataManagement;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use LVA\Models\Division;
use LVA\Models\Season;
use LVA\User;
use Tests\Browser\Pages\Resources\SeasonsPage;
use Tests\DuskTestCase;

class SeasonResourceTest extends DuskTestCase
{
    public function testRedirectIfNotAdmin()
    {
        $page = new SeasonsPage();

        $this->browse(function (Browser $browser) use ($page) {
            $season = factory(Season::class)->create();

            $browser->visit($page->indexUrl())
                ->assertRouteIs('login');

            $browser->visit($page->createUrl())
                ->assertRouteIs('login');

            $browser->visit($page->showUrl($season->id))
                ->assertRouteIs('login');

            $browser->visit($page->editUrl($season->id))
                ->assertRouteIs('login');

        });
    }

    public function testListSeasons()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Collection $seasons */
            $seasons = factory(Season::class)->times(20)->create();

            $page1 = $seasons->slice(0, 15);
            $page2 = $seasons->slice(15, 5);

            $page = new SeasonsPage();
            $browser->visit($page)
                ->assertSeeIn($page->breadcrumb, 'Seasons')
                ->assertSeeLink('New season')
                ->with('tbody', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $season) {
                        $table->with("tr:nth-child($child)", function ($row) use ($season) {
                            $linkText = $season->season;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText);
                        });
                        $child++;
                    }
                })
                ->with($page->pageNavigation, function ($nav) {
                    $nav->clickLink(2);
                })
                ->assertPathIs($page->indexUrl())
                ->with('tbody', function ($table) use ($page2) {
                    $child = 1;
                    foreach ($page2 as $season) {
                        $table->with("tr:nth-child($child)", function ($row) use ($season) {
                            $linkText = $season->season;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText);
                        });
                        $child++;
                    }
                });
        });
    }

    public function testAddSeason()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $page = new SeasonsPage();

            // Check we can add a season from the landing page
            $browser->visit($page)
                ->clickLink('New season')
                ->assertPathIs($page->createUrl());

            // All fields missing
            $browser->visit($page->createUrl())
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@season-error', 'The season field is required.')
                ->assertVisible('@form-errors');

            /** @var Season $season */
            $season = factory(Season::class)->make();
            // Brand new season
            $browser->visit($page->createUrl())
                ->type('season', $season->season)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Season added!');

            // Add the same season
            $browser->visit($page->createUrl())
                ->type('season', $season->season)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@season-error', 'The season already exists.')
                ->assertVisible('@form-errors');
        });
    }

    public function testEditSeason()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Season $season */
            $season = factory(Season::class)->create();

            $page = new SeasonsPage();

            // Check we can edit a season from the landing page
            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs($page->editUrl($season->id));

            // Don't change anything
            $browser->visit($page->editUrl($season->id))
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Season updated!');

            /** @var Season $newSeason */
            $newSeason = factory(Season::class)->make();

            // Edit all details
            $browser->visit($page->editUrl($season->id))
                ->type('season', $newSeason->season)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Season updated!');

            /** @var Season $newSeason */
            $newSeason = factory(Season::class)->create();

            // Use an already existing season
            $browser->visit($page->editUrl($season->id))
                ->type('season', $newSeason->season)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($season->id))
                ->assertSeeIn('@season-error', 'The season already exists.')
                ->assertVisible('@form-errors');
        });
    }

    public function testShowSeason()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Season $season */
            $season = factory(Season::class)->create();
            $linkText = $season->season;

            $page = new SeasonsPage();

            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) use ($linkText) {
                    $table->clickLink($linkText);
                })
                ->assertPathIs($page->showUrl($season->id))
                ->assertSeeIn('tbody tr td:nth-child(1)', $season->id)
                ->assertSeeIn('tbody tr td:nth-child(2)', $season->season);
        });
    }

    public function testDeleteSeason()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Season $season */
            $season = factory(Season::class)->create();

            $page = new SeasonsPage();

            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('No');
                })
                ->assertDontSee('Season deleted!')
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Season deleted!');

            // Delete season with existing division
            $season = factory(Season::class)->create();
            factory(Division::class)->create(['season_id' => $season->id]);
            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Cannot delete because they are existing divisions in this season.');
        });
    }
}
