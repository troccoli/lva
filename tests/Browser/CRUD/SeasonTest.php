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
    public function testListingAllSeasons(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

            $browser->visit('/seasons')
                ->assertSeeIn('@list', 'There are no seasons yet.');

            factory(Season::class)->create(['year' => 2000]);
            factory(Season::class)->create(['year' => 2010]);
            factory(Season::class)->create(['year' => 2002]);
            factory(Season::class)->create(['year' => 2003]);
            factory(Season::class)->create(['year' => 2012]);
            factory(Season::class)->create(['year' => 2011]);
            factory(Season::class)->create(['year' => 1999]);
            factory(Season::class)->create(['year' => 2006]);

            $browser->visit('/seasons')
                ->assertSeeLink('New season')
                ->with('@list', function (Browser $table): void {
                    $table->assertSeeIn('thead tr:nth-child(1)', 'Season');

                    $table->assertSeeIn('tbody tr:nth-child(1)', '2012/13');
                    $table->assertSeeIn('tbody tr:nth-child(2)', '2011/12');
                    $table->assertSeeIn('tbody tr:nth-child(3)', '2010/11');
                    $table->assertSeeIn('tbody tr:nth-child(4)', '2006/07');
                    $table->assertSeeIn('tbody tr:nth-child(5)', '2003/04');
                    $table->assertDontSee('2002/03');
                    $table->assertDontSee('2000/01');
                    $table->assertDontSee('1999/00');
                })
                ->with('div.pagination', function (Browser $nav): void {
                    $nav->clickLink(2);
                })
                ->assertPathIs('/seasons')
                ->assertQueryStringHas('page', 2)
                ->with('@list', function (Browser $table): void {
                    $table->assertDontSee('2012/13');
                    $table->assertDontSee('2011/12');
                    $table->assertDontSee('2010/11');
                    $table->assertDontSee('2006/07');
                    $table->assertDontSee('2003/04');
                    $table->assertSeeIn('tbody tr:nth-child(1)', '2002/03');
                    $table->assertSeeIn('tbody tr:nth-child(2)', '2000/01');
                    $table->assertSeeIn('tbody tr:nth-child(3)', '1999/00');
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddingASeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

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
            $this->assertDatabaseMissing('seasons', ['year' => 2000]);

            // Brand new season
            $browser->visit('/seasons/create')
                ->type('year', '2000')
                ->press('ADD SEASON')
                ->assertPathIs('/seasons')
                ->assertSee('Season added!');
            $this->assertDatabaseHas('seasons', ['year' => 2000]);

            // Add the same season
            $browser->visit('/seasons/create')
                ->type('year', '2000')
                ->press('ADD SEASON')
                ->assertPathIs('/seasons/create')
                ->assertSeeIn('@year-error', 'The season already exists.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditingASeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

            $browser->visit("/seasons/1/edit")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $seasonId = factory(Season::class)->create(['year' => 2001])->getId();

            // Check we can edit a season from the landing page
            $browser->visit('/seasons')
                ->with("@season-$seasonId-row", function (Browser $table): void {
                    $table->clickLink('Update');
                })
                ->assertPathIs("/seasons/$seasonId/edit");

            // Check the form
            $browser->visit("/seasons/$seasonId/edit")
                ->assertInputValue('year', '2001')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // Don't change anything
            $browser->visit("/seasons/$seasonId/edit")
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons')
                ->assertSee('Season updated!');
            $this->assertDatabaseHas('seasons', ['year' => '2001']);

            // Remove required fields
            $browser->visit("/seasons/$seasonId/edit")
                ->type('year', ' ')// This is to get around the HTML5 validation on the browser
                ->press('SAVE CHANGES')
                ->assertPathIs("/seasons/$seasonId/edit")
                ->assertSeeIn('@year-error', 'The year is required.');
            $this->assertDatabaseHas('seasons', ['year' => '2001']);

            // Edit all details
            $browser->visit("/seasons/$seasonId/edit")
                ->type('year', '2000')
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons')
                ->assertSee('Season updated!');
            $this->assertDatabaseMissing('seasons', ['year' => '2001']);
            $this->assertDatabaseHas('seasons', ['year' => '2000']);

            // Use an already existing season
            factory(Season::class)->create(['year' => 1999]);
            $browser->visit("/seasons/$seasonId/edit")
                ->type('year', '1999')
                ->press('SAVE CHANGES')
                ->assertPathIs("/seasons/$seasonId/edit")
                ->assertSeeIn('@year-error', 'The season already exists.');
            $this->assertDatabaseHas('seasons', ['id' => $seasonId, 'year' => '2000']);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeletingASeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

            $seasonId = factory(Season::class)->create()->getId();

            $browser->visit('/seasons')
                ->within("@season-$seasonId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Season deleted!');
            $this->assertDatabaseHas('seasons', ['id' => $seasonId]);

            $browser->visit('/seasons')
                ->within("@season-$seasonId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Season deleted!');
            $this->assertDatabaseMissing('seasons', ['id' => $seasonId]);

            // Delete season with existing competition
            $seasonId = factory(Season::class)->create()->getId();
            factory(Competition::class)->create(['season_id' => $seasonId]);
            $browser->visit('/seasons')
                ->within("@season-$seasonId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Cannot delete because there are existing competitions in this season!');
            $this->assertDatabaseHas('seasons', ['id' => $seasonId]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testViewingTheSeasonCompetitions(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create()->givePermissionTo('manage raw data'));

            $seasonId = factory(Season::class)->create()->getId();

            factory(Competition::class)->create(['name' => 'MP', 'season_id' => $seasonId]);
            factory(Competition::class)->create(['name' => 'WP']);

            $browser->visit('/seasons')
                ->within("@season-$seasonId-row", function (Browser $row): void {
                    $row->clickLink('View');
                })
                ->assertPathIs("/seasons/$seasonId/competitions")
                ->assertSeeIn('@list', 'MP')
                ->assertDontSeeIn('@list', 'WP');
        });
    }
}
