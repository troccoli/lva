<?php

namespace Tests\Browser\Admin\DataManagement;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use LVA\Models\Division;
use LVA\Models\Fixture;
use LVA\Models\Season;
use LVA\User;
use Tests\Browser\Pages\Resources\DivisionsPage;
use Tests\DuskTestCase;

class DivisionResourceTest extends DuskTestCase
{
    public function testRedirectIfNotAdmin()
    {
        $page = new DivisionsPage();

        $this->browse(function (Browser $browser) use ($page) {
            $division = factory(Division::class)->create();

            $browser->visit($page->indexUrl())
                ->assertRouteIs('login');

            $browser->visit($page->createUrl())
                ->assertRouteIs('login');

            $browser->visit($page->showUrl($division->id))
                ->assertRouteIs('login');

            $browser->visit($page->editUrl($division->id))
                ->assertRouteIs('login');

        });
    }

    public function testListDivisions()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Collection $divisions */
            $divisions = factory(Division::class)->times(20)->create();

            $page1 = $divisions->slice(0, 15);
            $page2 = $divisions->slice(15, 5);

            $page = new DivisionsPage();
            $browser->visit($page)
                ->assertSeeIn($page->breadcrumb, 'Divisions')
                ->assertSeeLink('New division')
                ->with('tbody', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $division) {
                        $table->with("tr:nth-child($child)", function ($row) use ($division) {
                            $linkText = $division->division;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $division->season->season)
                                ->assertSeeIn('td:nth-child(2)', $linkText);
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
                    foreach ($page2 as $division) {
                        $table->with("tr:nth-child($child)", function ($row) use ($division) {
                            $linkText = $division->division;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $division->season->season)
                                ->assertSeeIn('td:nth-child(2)', $linkText);
                        });
                        $child++;
                    }
                });
        });
    }

    public function testAddDivision()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $page = new DivisionsPage();

            // Check we can add a division from the landing page
            $browser->visit($page)
                ->clickLink('New division')
                ->assertPathIs($page->createUrl());

            /** @var Division $division */
            $division = factory(Division::class)->make();
            // Brand new division
            $browser->visit($page->createUrl())
                ->select('season_id', $division->season_id)
                ->type('division', $division->division)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Division added!');

            // Add the same division
            $browser->visit($page->createUrl())
                ->select('season_id', $division->season_id)
                ->type('division', $division->division)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@division-error', 'The division already exists in the same season.')
                ->assertVisible('@form-errors');

            // Missing fields
            $browser->visit($page->createUrl())
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@season-id-error', 'The season id field is required.')
                ->assertSeeIn('@division-error', 'The division field is required.')
                ->assertVisible('@form-errors');
        });
    }

    public function testEditDivision()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Division $division */
            $division = factory(Division::class)->create();

            $page = new DivisionsPage();

            // Check we can edit a division from the landing page
            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs($page->editUrl($division->id));

            // Don't change anything
            $browser->visit($page->editUrl($division->id))
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Division updated!');

            /** @var Division $newDivision */
            $newDivision = factory(Division::class)->make();

            // Change the name of the division
            $browser->visit($page->editUrl($division->id))
                ->type('division', $newDivision->division)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Division updated!');

            // Move division to a different season
            $season = factory(Season::class)->create();
            $browser->visit($page->editUrl($division->id))
                ->select('season_id', $season->id)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Division updated!');
            $division->season_id = $season->id;

            // Already existing division in the same season
            /** @var Division $newDivision */
            $newDivision = factory(Division::class)->create(['season_id' => $division->season_id]);
            $browser->visit($page->editUrl($division->id))
                ->type('division', $newDivision->division)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($division->id))
                ->assertSeeIn('@division-error', 'The division already exists in the same season.')
                ->assertVisible('@form-errors');
        });
    }

    public function testShowDivision()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Division $division */
            $division = factory(Division::class)->create();
            $linkText = $division->division;

            $page = new DivisionsPage();

            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) use ($linkText) {
                    $table->clickLink($linkText);
                })
                ->assertPathIs($page->showUrl($division->id))
                ->assertSeeIn('tbody tr td:nth-child(1)', $division->id)
                ->assertSeeIn('tbody tr td:nth-child(2)', $division->season->season)
                ->assertSeeIn('tbody tr td:nth-child(3)', $division->division);
        });
    }

    public function testDeleteDivision()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Division $division */
            $division = factory(Division::class)->create();

            $page = new DivisionsPage();

            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('No');
                })
                ->assertDontSee('Division deleted!')
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Division deleted!');

            // Delete division with existing fixtures
            $division = factory(Division::class)->create();
            factory(Fixture::class)->create(['division_id' => $division->id]);
            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Cannot delete because they are existing fixtures in this division.');
        });
    }
}
