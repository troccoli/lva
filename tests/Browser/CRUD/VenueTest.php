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
    public function testListingAllVenues(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

            $browser->visit('/venues')
                ->assertSeeIn('@list', 'There are no venues yet.');

            /** @var Collection $venues */
            $venues = factory(Venue::class)->times(25)->create();

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
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

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
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

            $browser->visit('/venues/1/edit')
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $venueId = factory(Venue::class)->create(['name' => 'Lewisham Sports Hall'])->getId();

            // Check we can edit a venue from the landing page
            $browser->visit('/venues')
                ->with("@venue-$venueId-row", function (Browser $table): void {
                    $table->clickLink('Update');
                })
                ->assertPathIs("/venues/$venueId/edit");

            // Check the form
            $browser->visit("/venues/$venueId/edit")
                ->assertInputValue('name', 'Lewisham Sports Hall')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // Don't change anything
            $browser->visit("/venues/$venueId/edit")
                ->press('SAVE CHANGES')
                ->assertPathIs('/venues')
                ->assertSee('Venue updated!')
                ->assertSeeIn('@list', 'Lewisham Sports Hall');
            $this->assertDatabaseHas('venues', [
                'id'   => $venueId,
                'name' => 'Lewisham Sports Hall',
            ]);

            // Remove required fields
            $browser->visit("/venues/$venueId/edit")
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('SAVE CHANGES')
                ->assertPathIs("/venues/$venueId/edit")
                ->assertSeeIn('@name-error', 'The name is required.');
            $this->assertDatabaseHas('venues', [
                'id'   => $venueId,
                'name' => 'Lewisham Sports Hall',
            ]);
            // Edit all details
            $browser->visit("/venues/$venueId/edit")
                ->type('name', 'Sobell S.C.')
                ->press('SAVE CHANGES')
                ->assertPathIs('/venues')
                ->assertSee('Venue updated!');
            $this->assertDatabaseHas('venues', [
                'id'   => $venueId,
                'name' => 'Sobell S.C.',
            ]);

            // Use an already existing venue
            factory(Venue::class)->create(['name' => 'Olympic Stadium']);
            $browser->visit("/venues/$venueId/edit")
                ->type('name', 'Olympic Stadium')
                ->press('SAVE CHANGES')
                ->assertPathIs("/venues/$venueId/edit")
                ->assertSeeIn('@name-error', 'The venue already exists.');
            $this->assertDatabaseHas('venues', [
                'id'   => $venueId,
                'name' => 'Sobell S.C.',
            ]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeletingAVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

            $venueId = factory(Venue::class)->create(['name' => 'Sobell S.C.'])->getId();

            $browser->visit('/venues')
                ->within("@venue-$venueId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Venue deleted!');
            $this->assertDatabaseHas('venues', ['id' => $venueId]);

            $browser->visit('/venues')
                ->within("@venue-$venueId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Venue deleted!');
            $this->assertDatabaseMissing('venues', ['id' => $venueId]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testViewVenue(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

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
