<?php

namespace Tests\Browser\CRUD;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VenueTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListingAllVenues(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/venues')
                    ->assertSeeIn('@list', 'There are no venues yet.');

            /** @var Collection $venues */
            $venues = Venue::factory()->count(25)->create();

            $page1 = $venues->slice(0, 15);
            $page2 = $venues->slice(15, 15);

            $browser->visit('/venues')
                    ->assertSeeLink('New venue')
                    ->with('@list', function (Browser $table) use ($page1): void {
                        $table->assertSeeIn('thead tr:nth-child(1) th:nth-child(1)', 'Venue');

                        $child = 1;
                        foreach ($page1 as $venue) {
                            /** @var Venue $venue */
                            $table->assertSeeIn("tbody tr:nth-child($child) td:nth-child(1)", $venue->getName());
                            $child++;
                        }
                    })
                    ->with('div.pagination', function (Browser $nav): void {
                        $nav->clickLink(2);
                    })
                    ->assertPathIs('/venues')
                    ->assertQueryStringHas('page', 2)
                    ->with('@list', function (Browser $table) use ($page2): void {
                        $table->assertSeeIn('thead tr:nth-child(1) th:nth-child(1)', 'Venue');

                        $child = 1;
                        foreach ($page2 as $venue) {
                            /** @var Venue $venue */
                            $table->assertSeeIn("tbody tr:nth-child($child) td:nth-child(1)", $venue->getName());
                            $child++;
                        }
                    });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddingAVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

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
            $this->assertDatabaseMissing('venues', ['name' => 'Olympic Stadium']);

            // Brand new venue
            $browser->visit('/venues/create')
                    ->type('name', 'Olympic Stadium')
                    ->press('ADD VENUE')
                    ->assertPathIs('/venues')
                    ->assertSee('Venue added!');
            $this->assertDatabaseHas('venues', ['name' => ['Olympic Stadium']]);

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
    public function testEditingAVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/venues/1/edit')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $venue = Venue::factory()->create(['name' => 'Lewisham Sports Hall']);

            // Check we can edit a venue from the landing page
            $browser->visit('/venues')
                    ->with("@venue-{$venue->getId()}-row", function (Browser $table): void {
                        $table->clickLink('Update');
                    })
                    ->assertPathIs("/venues/{$venue->getId()}/edit");

            // Check the form
            $browser->visit("/venues/{$venue->getId()}/edit")
                    ->assertInputValue('name', 'Lewisham Sports Hall')
                    ->assertVisible('@submit-button')
                    ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // Don"t change anything
            $browser->visit("/venues/{$venue->getId()}/edit")
                    ->press('SAVE CHANGES')
                    ->assertPathIs('/venues')
                    ->assertSee('Venue updated!')
                    ->assertSeeIn('@list', 'Lewisham Sports Hall');
            $this->assertDatabaseHas(
                'venues',
                [
                    'id' => $venue->getId(),
                    'name' => 'Lewisham Sports Hall',
                ]
            );

            // Remove required fields
            $browser->visit("/venues/{$venue->getId()}/edit")
                    ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/venues/{$venue->getId()}/edit")
                    ->assertSeeIn('@name-error', 'The name is required.');
            $this->assertDatabaseHas(
                'venues',
                [
                    'id' => $venue->getId(),
                    'name' => 'Lewisham Sports Hall',
                ]
            );
            // Edit all details
            $browser->visit("/venues/{$venue->getId()}/edit")
                    ->type('name', 'Sobell S.C.')
                    ->press('SAVE CHANGES')
                    ->assertPathIs('/venues')
                    ->assertSee('Venue updated!');
            $this->assertDatabaseHas(
                'venues',
                [
                    'id' => $venue->getId(),
                    'name' => 'Sobell S.C.',
                ]
            );

            // Use an already existing venue
            Venue::factory()->create(['name' => 'Olympic Stadium']);
            $browser->visit("/venues/{$venue->getId()}/edit")
                    ->type('name', 'Olympic Stadium')
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/venues/{$venue->getId()}/edit")
                    ->assertSeeIn('@name-error', 'The venue already exists.');
            $this->assertDatabaseHas(
                'venues',
                [
                    'id' => $venue->getId(),
                    'name' => 'Sobell S.C.',
                ]
            );
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeletingAVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $venue = Venue::factory()->create(['name' => 'Sobell S.C.']);

            $browser->visit('/venues')
                    ->within("@venue-{$venue->getId()}-row", function (Browser $row): void {
                        $row->press('Delete');
                    })
                    ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                        $modal->assertSee('Are you sure?')
                              ->press('Cancel')
                              ->pause(1000);
                    })
                    ->assertDontSee('Venue deleted!');
            $this->assertDatabaseHas('venues', ['id' => $venue->getId()]);

            $browser->visit('/venues')
                    ->within("@venue-{$venue->getId()}-row", function (Browser $row): void {
                        $row->press('Delete');
                    })
                    ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                        $modal->assertSee('Are you sure?')
                              ->press('Confirm')
                              ->pause(1000);
                    })
                    ->assertSee('Venue deleted!');
            $this->assertDatabaseMissing('venues', ['id' => $venue->getId()]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testViewVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            /** @var Venue $venue */
            $venue = Venue::factory()->create(['name' => 'Olympic Stadium']);

            $browser->visit("/venues/{$venue->getId()}")
                    ->assertSeeIn('h1', 'Olympic Stadium')
                    ->within('table', function (Browser $table): void {
                        $table->assertSee('Olympic Stadium');
                    });
        });
    }
}
