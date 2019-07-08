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
                ->with('tbody', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $season) {
                        $table->with("tr:nth-child($child)", function ($row) use ($season) {
                            $row->assertSeeIn('td:nth-child(1)', $season->name);
                        });
                        $child++;
                    }
                })
                ->with('div.pagination', function ($nav) {
                    $nav->clickLink(2);
                })
                ->assertPathIs('/seasons')
                ->assertQueryStringHas('page', 2)
                ->with('tbody', function ($table) use ($page2) {
                    $child = 1;
                    foreach ($page2 as $season) {
                        $table->with("tr:nth-child($child)", function ($row) use ($season) {
                            $row->assertSeeIn('td:nth-child(1)', $season->name);
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
                ->type('name', $season->name)
                ->press('ADD SEASON')
                ->assertPathIs('/seasons')
                ->assertSee('Season added!');

            // Add the same season
            $browser->visit('/seasons/create')
                ->type('name', $season->name)
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
                ->assertPathIs('/seasons/' . $season->id . '/edit');

            // Don't change anything
            $browser->visit('/seasons/' . $season->id . '/edit')
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons')
                ->assertSee('Season updated!');

            /** @var Season $newSeason */
            $newSeason = factory(Season::class)->make();

            // Remove required fields
            $browser->visit('/seasons/' . $season->id . '/edit')
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons/' . $season->id . '/edit')
                ->assertSeeIn('@name-error', 'The name is required.');

            // Edit all details
            $browser->visit('/seasons/' . $season->id . '/edit')
                ->type('name', $newSeason->name)
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons')
                ->assertSee('Season updated!');

            $newSeason = factory(Season::class)->create();

            // Use an already existing season
            $browser->visit('/seasons/' . $season->id . '/edit')
                ->type('name', $newSeason->name)
                ->press('SAVE CHANGES')
                ->assertPathIs('/seasons/' . $season->id . '/edit')
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
                ->assertSee($season->name);
            $browser->visit('/seasons')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal) {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Season deleted!')
                ->assertDontSee($season->name);

            // Delete season with existing competition
            $season = factory(Season::class)->create();
            factory(Competition::class)->create(['season_id' => $season->id]);
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
