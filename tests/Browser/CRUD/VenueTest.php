<?php

namespace Tests\Browser\CRUD;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use App\Models\Venue;
use App\Models\User;
use Tests\DuskTestCase;

class VenueTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListVenues(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit('/venues')
                ->assertSeeIn('@list', 'There are no venues yet.');

            /** @var Collection $venues */
            $venues = factory(Venue::class)->times(25)->create();

            $page1 = $venues->slice(0, 15);
            $page2 = $venues->slice(15, 15);

            $browser->visit('/venues')
                ->assertSeeLink('New venue')
                ->with('@list', function (Browser $table) use ($page1): void {
                    $row = 1;
                    foreach ($page1 as $venue) {
                        /** @var Venue $venue */
                        $table->with("tr:nth-child($row)", function (Browser $row) use ($venue): void {
                            $row->assertSeeIn('td:nth-child(1)', $venue->getName());
                        });
                        $row++;
                    }
                })
                ->with('div.pagination', function (Browser $nav): void {
                    $nav->clickLink(2);
                })
                ->assertPathIs('/venues')
                ->assertQueryStringHas('page', 2)
                ->with('@list', function (Browser $table) use ($page2): void {
                    $row = 1;
                    foreach ($page2 as $venue) {
                        /** @var Venue $venue */
                        $table->with("tr:nth-child($row)", function (Browser $row) use ($venue): void {
                            $row->assertSeeIn('td:nth-child(1)', $venue->getName());
                        });
                        $row++;
                    }
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            // Check we can add a venue from the landing page
            $browser->visit('/venues')
                ->clickLink('New venue')
                ->assertPathIs('/venues/create');

            // Check the form
            $browser->visit('/venues/create')
                ->assertInputValue('name', '')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'ADD VENUE');

            // All fields missing
            $browser->visit('/venues/create')
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('ADD VENUE')
                ->assertPathIs('/venues/create')
                ->assertSeeIn('@name-error', 'The name is required.');

            // Brand new venue
            $browser->visit('/venues/create')
                ->type('name', 'Olympic Stadium')
                ->press('ADD VENUE')
                ->assertPathIs('/venues')
                ->assertSee('Venue added!')
                ->assertSeeIn('@list', 'Olympic Stadium');

            // Add the same venue
            $browser->visit('/venues/create')
                ->type('name', 'Olympic Stadium')
                ->press('ADD VENUE')
                ->assertPathIs('/venues/create')
                ->assertSeeIn('@name-error', 'The venue already exists.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Venue $venue */
            $venue = factory(Venue::class)->create(['name' => 'Lewisham Sports Hall']);

            // Check we can edit a venue from the landing page
            $browser->visit('/venues')
                ->with('@list', function (Browser $table): void {
                    $table->clickLink('Update');
                })
                ->assertPathIs('/venues/' . $venue->getId() . '/edit');

            // Check the form
            $browser->visit('/venues/' . $venue->getId() . '/edit')
                ->assertInputValue('name', 'Lewisham Sports Hall')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // Don't change anything
            $browser->visit('/venues/' . $venue->getId() . '/edit')
                ->press('SAVE CHANGES')
                ->assertPathIs('/venues')
                ->assertSee('Venue updated!')
                ->assertSeeIn('@list', 'Lewisham Sports Hall');

            // Remove required fields
            $browser->visit('/venues/' . $venue->getId() . '/edit')
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('SAVE CHANGES')
                ->assertPathIs('/venues/' . $venue->getId() . '/edit')
                ->assertSeeIn('@name-error', 'The name is required.');

            // Edit all details
            $browser->visit('/venues/' . $venue->getId() . '/edit')
                ->type('name', 'Sobell S.C.')
                ->press('SAVE CHANGES')
                ->assertPathIs('/venues')
                ->assertSee('Venue updated!')
                ->assertSeeIn('@list', 'Sobell S.C.')
                ->assertDontSeeIn('@list', 'Lewisham Sports Hall');

            factory(Venue::class)->create(['name' => 'Olympic Stadium']);

            // Use an already existing venue
            $browser->visit('/venues/' . $venue->getId() . '/edit')
                ->type('name', 'Olympic Stadium')
                ->press('SAVE CHANGES')
                ->assertPathIs('/venues/' . $venue->getId() . '/edit')
                ->assertSeeIn('@name-error', 'The venue already exists.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeleteVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            factory(Venue::class)->create(['name' => 'Sobell S.C.']);

            $browser->visit('/venues')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Venue deleted!')
                ->assertSee('Sobell S.C.');
            $browser->visit('/venues')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Venue deleted!')
                ->assertDontSee('Sobell S.C.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testViewVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Venue $venue */
            $venue = factory(Venue::class)->create(['name' => 'Olympic Stadium']);

            $browser->visit('/venues/' . $venue->getId())
                ->assertSeeIn('h1', 'Olympic Stadium')
                ->within('table', function (Browser $table): void {
                    $table->assertSee('Olympic Stadium');
                });
        });
    }
}
