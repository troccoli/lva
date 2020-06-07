<?php

namespace Tests\Browser\CRUD;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use App\Models\Club;
use App\Models\User;
use Tests\DuskTestCase;

class ClubTest extends DuskTestCase
{
    private $venueId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->venueId = factory(Venue::class)->create(['name' => 'Olympic Stadium'])->getId();
    }

    /**
     * @throws \Throwable
     */
    public function testListingAllClubs(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/clubs')
                ->assertSeeIn('@list', 'There are no clubs yet.');

            /** @var Collection $clubs */
            $clubs = factory(Club::class)
                ->times(25)
                ->create(['venue_id' => $this->venueId])
                ->sortBy('name');

            $page1 = $clubs->slice(0, 15);
            $page2 = $clubs->slice(15, 15);

            $browser->visit('/clubs')
                ->assertSeeLink('New club')
                ->with('@list', function (Browser $table) use ($page1): void {
                    $table->assertSeeIn('thead tr:nth-child(1) th:nth-child(1)', 'Club');
                    $table->assertSeeIn('thead tr:nth-child(1) th:nth-child(2)', 'Venue');

                    $child = 1;
                    foreach ($page1 as $club) {
                        /** @var Club $club */
                        $table->assertSeeIn("tbody tr:nth-child($child) td:nth-child(1)", $club->getName());
                        $table->assertSeeIn("tbody tr:nth-child($child) td:nth-child(2)", 'Olympic Stadium');
                        $child++;
                    }
                })
                ->with('div.pagination', function (Browser $nav): void {
                    $nav->clickLink(2);
                })
                ->assertPathIs('/clubs')
                ->assertQueryStringHas('page', 2)
                ->with('@list', function (Browser $table) use ($page2): void {
                    $child = 1;
                    foreach ($page2 as $club) {
                        /** @var Club $club */
                        $table->assertSeeIn("tbody tr:nth-child($child) td:nth-child(1)", $club->getName());
                        $table->assertSeeIn("tbody tr:nth-child($child) td:nth-child(2)", 'Olympic Stadium');
                        $child++;
                    }
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddingAClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            // Check we can add a club from the landing page
            $browser->visit('/clubs')
                ->clickLink('New club')
                ->assertPathIs('/clubs/create');

            $browser->visit("/clubs/create")
                ->assertSee('Add a new club');

            // Check the form
            $browser->visit('/clubs/create')
                ->assertInputValue('@inputName-field', '')
                ->assertSelected('@selectVenue-field', '')
                ->assertSeeIn('@selectVenue-field', 'No venue')
                ->assertSeeIn('@selectVenue-field', 'Olympic Stadium')
                ->assertSelectHasOptions('@selectVenue-field', ["", $this->venueId])
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'Add club');

            // All fields missing
            $browser->visit('/clubs/create')
                ->type('@inputName-field', ' ')// This is to get around the HTML5 validation on the browser
                ->press('Add club')
                ->assertPathIs('/clubs/create')
                ->assertSeeIn('@name-error', 'The name is required.');
            $this->assertDatabaseMissing('clubs', ['name' => 'London Giants', 'venue_id' => null]);

            // Brand new club
            $browser->visit('/clubs/create')
                ->type('@inputName-field', 'London Giants')
                ->press('Add club')
                ->assertPathIs('/clubs')
                ->assertSee('Club added!');
            $this->assertDatabaseHas('clubs', ['name' => 'London Giants', 'venue_id' => null]);

            // Add a club with a venue
            $browser->visit('/clubs/create')
                ->type('@inputName-field', 'London Spiders')
                ->select('@selectVenue-field', 'Olympic Stadium')
                ->press('Add club')
                ->assertPathIs('/clubs')
                ->assertSee('Club added!');
            $this->assertDatabaseMissing('clubs', ['name' => 'London Spiders', 'venue_id' => $this->venueId]);

            // Add the same club
            $browser->visit('/clubs/create')
                ->type('@inputName-field', 'London Giants')
                ->press('Add club')
                ->assertPathIs('/clubs/create')
                ->assertSeeIn('@name-error', 'The club already exists.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditingAClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit("/clubs/1/edit")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            /** @var Venue $theBox */
            $theBox = factory(Venue::class)->create(['name' => 'The Box']);
            $clubId = aClub()
                ->withName('Global Warriors')
                ->withVenue($theBox)
                ->build()
                ->getId();

            // Check we can edit a club from the landing page
            $browser->visit('/clubs')
                ->with("@club-$clubId-row", function (Browser $table): void {
                    $table->clickLink('Update');
                })
                ->assertPathIs("/clubs/$clubId/edit")
                ->assertSee("Edit the Global Warriors club");

            // Check the form
            $browser->visit("/clubs/$clubId/edit")
                ->assertInputValue('@inputName-field', 'Global Warriors')
                ->assertSelected('@selectVenue-field', $theBox->getId())
                ->assertSeeIn('@selectVenue-field', 'The Box')
                ->assertSeeIn('@selectVenue-field', 'No venue')
                ->assertSeeIn('@selectVenue-field', 'Olympic Stadium')
                ->assertSelectHasOptions('@selectVenue-field', ["", $theBox->getId(), $this->venueId])
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'Save changes');

            // Don't change anything
            $browser->visit("/clubs/$clubId/edit")
                ->press('Save changes')
                ->assertPathIs('/clubs')
                ->assertSee('Club updated!');
            $this->assertDatabaseHas('clubs', ['name' => 'Global Warriors', 'venue_id' => $theBox->getId()]);

            // Remove required fields
            $browser->visit("/clubs/$clubId/edit")
                ->type('@inputName-field', ' ')// This is to get around the HTML5 validation on the browser
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/edit")
                ->assertSeeIn('@name-error', 'The name is required.');

            // Edit all details
            $browser->visit("/clubs/$clubId/edit")
                ->type('@inputName-field', 'London Giants')
                ->select('@selectVenue-field', $this->venueId)
                ->press('Save changes')
                ->assertPathIs('/clubs')
                ->assertSee('Club updated!');
            $this->assertDatabaseMissing('clubs', ['name' => 'Global Warriors', 'venue_id' => $theBox->getId()]);
            $this->assertDatabaseHas('clubs', ['name' => 'London Giants', 'venue_id' => $this->venueId]);

            // Use an already existing club
            aClub()->withName('London Spiders')->build();
            $browser->visit("/clubs/$clubId/edit")
                ->type('@inputName-field', 'London Spiders')
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/edit")
                ->assertSeeIn('@name-error', 'The club already exists.');
            $this->assertDatabaseHas('clubs', ['name' => 'London Giants', 'venue_id' => $this->venueId]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeletingAClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $clubId = aClub()->build()->getId();

            $browser->visit('/clubs')
                ->within("@club-$clubId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Club deleted!');
            $this->assertDatabaseHas('clubs', ['id' => $clubId]);

            $browser->visit('/clubs')
                ->within("@club-$clubId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Club deleted!');
            $this->assertDatabaseMissing('clubs', ['id' => $clubId]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testViewingTheClubTeams(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            /** @var Club $club */
            $club = aClub()->build();

            aTeam()->inClub($club)->withName('Team A')->build();
            aTeam()->withName('Team B')->build();

            $browser->visit('/clubs')
                ->within("@club-{$club->getId()}-row", function (Browser $row): void {
                    $row->clickLink('View');
                })
                ->assertPathIs('/clubs/' . $club->getId() . '/teams')
                ->assertSeeIn('@list', 'Team A')
                ->assertDontSeeIn('@list', 'Team B');
        });
    }
}
