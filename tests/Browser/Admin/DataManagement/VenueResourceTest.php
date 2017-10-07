<?php

namespace Tests\Browser\Admin\DataManagement;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use LVA\Models\Division;
use LVA\Models\Fixture;
use LVA\Models\Venue;
use LVA\User;
use Tests\Browser\Pages\Resources\VenuesPage;
use Tests\DuskTestCase;

class VenueResourceTest extends DuskTestCase
{
    public function testRedirectIfNotAdmin()
    {
        $page = new VenuesPage();

        $this->browse(function (Browser $browser) use ($page) {
            $venue = factory(Venue::class)->create();

            $browser->visit($page->indexUrl())
                ->assertRouteIs('login');

            $browser->visit($page->createUrl())
                ->assertRouteIs('login');

            $browser->visit($page->showUrl($venue->id))
                ->assertRouteIs('login');

            $browser->visit($page->editUrl($venue->id))
                ->assertRouteIs('login');

        });
    }

    public function testListVenues()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Collection $venues */
            $venues = factory(Venue::class)->times(20)->create();

            $page1 = $venues->slice(0, 15);
            $page2 = $venues->slice(15, 5);

            $page = new VenuesPage();
            $browser->visit($page)
                ->assertSeeIn($page->breadcrumb, 'Venues')
                ->assertSeeLink('New venue')
                ->with('tbody', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $venue) {
                        $table->with("tr:nth-child($child)", function ($row) use ($venue) {
                            $linkText = $venue->venue;
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
                    foreach ($page2 as $venue) {
                        $table->with("tr:nth-child($child)", function ($row) use ($venue) {
                            $linkText = $venue->venue;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText);
                        });
                        $child++;
                    }
                });
        });
    }

    public function testAddVenue()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $page = new VenuesPage();

            // Check we can add a venue from the landing page
            $browser->visit($page)
                ->clickLink('New venue')
                ->assertPathIs($page->createUrl());

            // All fields missing
            $browser->visit($page->createUrl())
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@venue-error', 'The venue field is required.')
                ->assertSeeIn('@postcode-error', 'The postcode must be a valid UK postcode.')
                ->assertVisible('@form-errors');

            /** @var Venue $venue */
            $venue = factory(Venue::class)->make();
            // Brand new venue
            $browser->visit($page->createUrl())
                ->type('venue', $venue->venue)
                ->type('directions', $venue->directions)
                ->type('postcode', $venue->postcode)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Venue added!');

            // Add the same venue
            $browser->visit($page->createUrl())
                ->type('venue', $venue->venue)
                ->type('directions', $venue->directions)
                ->type('postcode', $venue->postcode)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@venue-error', 'The venue already exists.')
                ->assertVisible('@form-errors');

            $venue = factory(Venue::class)->make();
            // Invalid UK postcode
            $browser->visit($page->createUrl())
                ->type('venue', $venue->venue)
                ->type('postcode', 'AA12')
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@postcode-error', 'The postcode must be a valid UK postcode.')
                ->assertVisible('@form-errors');

            // Add a new venue without directions
            $browser->visit($page->createUrl())
                ->type('venue', $venue->venue)
                ->type('postcode', $venue->postcode)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Venue added!');
        });
    }

    public function testEditVenue()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Venue $venue */
            $venue = factory(Venue::class)->create();

            $page = new VenuesPage();

            // Check we can edit a venue from the landing page
            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs($page->editUrl($venue->id));

            // Don't change anything
            $browser->visit($page->editUrl($venue->id))
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Venue updated!');

            /** @var Venue $newVenue */
            $newVenue = factory(Venue::class)->make();

            // Edit all details
            $browser->visit($page->editUrl($venue->id))
                ->type('venue', $newVenue->venue)
                ->type('directions', $venue->directions)
                ->type('postcode', $venue->postcode)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Venue updated!');

            // Remove the directions
            $browser->visit($page->editUrl($venue->id))
                ->clear('directions')
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Venue updated!');

            /** @var Venue $newVenue */
            $newVenue = factory(Venue::class)->create();

            // Use an already existing venue
            $browser->visit($page->editUrl($venue->id))
                ->type('venue', $newVenue->venue)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($venue->id))
                ->assertSeeIn('@venue-error', 'The venue already exists.')
                ->assertVisible('@form-errors');

            // Use a wrong postcode
            $browser->visit($page->editUrl($venue->id))
                ->type('postcode', 'AA12')
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($venue->id))
                ->assertSeeIn('@postcode-error', 'The postcode must be a valid UK postcode.')
                ->assertVisible('@form-errors');
        });
    }

    public function testShowVenue()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Venue $venue */
            $venue = factory(Venue::class)->create();
            $linkText = $venue->venue;

            $page = new VenuesPage();

            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) use ($linkText) {
                    $table->clickLink($linkText);
                })
                ->assertPathIs($page->showUrl($venue->id))
                ->assertSeeIn('tbody tr td:nth-child(1)', $venue->id)
                ->assertSeeIn('tbody tr td:nth-child(2)', $venue->venue)
                ->assertSeeIn('tbody tr td:nth-child(3)', $venue->directions)
                ->assertSeeIn('tbody tr td:nth-child(4)', $venue->postcode);
        });
    }

    public function testDeleteVenue()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Venue $venue */
            $venue = factory(Venue::class)->create();

            $page = new VenuesPage();

            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('No');
                })
                ->assertDontSee('Venue deleted!')
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Venue deleted!');

            // Delete venue with existing fixture
            $venue = factory(Venue::class)->create();
            factory(Fixture::class)->create(['venue_id' => $venue->id]);
            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Cannot delete because they are existing fixtures at this venue.');
        });
    }
}
