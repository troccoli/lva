<?php

namespace Tests\Browser\Admin\DataManagement;

use App\Models\Competition;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use App\Models\Season;
use App\Models\User;
use Tests\DuskTestCase;

class SeasonTest extends DuskTestCase
{
    public function testListSeasons(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Collection $seasons */
            $seasons = factory(Season::class)->times(7)->create()->sortByDesc('name');

            $page1 = $seasons->slice(0, 5);
            $page2 = $seasons->slice(5, 2);

            $browser->visit('/seasons')
                ->assertSeeLink('New season')
                ->with('@list', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $season) {
                        $table->with("tr:nth-child($child)", function ($row) use ($season) {
                            $row->assertSeeIn('td:nth-child(1)', $season->getName());
                        });
                        $child++;
                    }
                })
                ->with('div.pagination', function ($nav) {
                    $nav->clickLink(2);
                })
                ->assertPathIs('/seasons')
                ->assertQueryStringHas('page', 2)
                ->with('@list', function ($table) use ($page2) {
                    $child = 1;
                    foreach ($page2 as $season) {
                        $table->with("tr:nth-child($child)", function ($row) use ($season) {
                            $row->assertSeeIn('td:nth-child(1)', $season->getName());
                        });
                        $child++;
                    }
                });
        });
    }

    public function testAddSeason(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            // Check we can add a season from the landing page
            $browser->visit('/seasons')
                ->clickLink('New season')
                ->assertPathIs('/seasons/create');

            // Check the form
            $browser->visit('/seasons/create')
                ->assertInputValue('name', '')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'ADD SEASON');

            // All fields missing
            $browser->visit('/seasons/create')
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('ADD SEASON')
                ->assertPathIs('/seasons/create')
                ->assertSeeIn('@name-error', 'The name is required.');

            /** @var Season $season */
            $season = factory(Season::class)->make();
            // Brand new season
            $browser->visit('/seasons/create')
                ->type('name', $season->getName())
                ->press('ADD SEASON')
                ->assertPathIs('/seasons')
                ->assertSee('Season added!');

            // Add the same season
            $browser->visit('/seasons/create')
                ->type('name', $season->getName())
                ->press('ADD SEASON')
                ->assertPathIs('/seasons/create')
                ->assertSeeIn('@name-error', 'The season already exists.');
        });
    }

    public function testEditSeason()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Season $season */
            $season = factory(Season::class)->create();

            // Check we can edit a season from the landing page
            $browser->visit('/seasons')
                ->with('@list', function ($table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs('/seasons/' . $season->getId() . '/edit');

            // Check the form
            $browser->visit('/seasons/' . $season->getId() . '/edit')
                ->assertInputValue('name', $season->getName())
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
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons/' . $season->getId() . '/edit')
                ->assertSeeIn('@name-error', 'The name is required.');

            // Edit all details
            $browser->visit('/seasons/' . $season->getId() . '/edit')
                ->type('name', $newSeason->getName())
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons')
                ->assertSee('Season updated!');

            $newSeason = factory(Season::class)->create();

            // Use an already existing season
            $browser->visit('/seasons/' . $season->getId() . '/edit')
                ->type('name', $newSeason->getName())
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons/' . $season->getId() . '/edit')
                ->assertSeeIn('@name-error', 'The season already exists.');
        });
    }

    public function testDeleteSeason()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Season $season */
            $season = factory(Season::class)->create();

            $browser->visit('/seasons')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal) {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Season deleted!')
                ->assertSee($season->getName());
            $browser->visit('/seasons')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal) {
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
                ->whenAvailable('.bootbox-confirm', function (Browser $modal) {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Cannot delete because there are existing competitions in this season!')
                ->assertSeeIn('@list', $season->getName());
        });
    }
}
