<?php

namespace Tests\Browser\CRUD;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CompetitionTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListAllCompetitionsForNonExistingSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit("/seasons/1/competitions/")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListAllCompetitionsForSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $seasonId = factory(Season::class)->create(['year' => 2001])->getId();

            $browser->loginAs(factory(User::class)->create());

            $browser->visit("/seasons/$seasonId/competitions/")
                ->assertSee("Competitions in the 2001/02 season")
                ->assertSeeIn('@list', 'There are no competitions in this season yet.');

            factory(Competition::class)->times(5)->create();
            $competitions = [
                1 => factory(Competition::class)->create(['season_id' => $seasonId, 'name' => 'London League']),
                2 => factory(Competition::class)->create(['season_id' => $seasonId, 'name' => 'Super8']),
                3 => factory(Competition::class)->create(['season_id' => $seasonId, 'name' => 'Youth Games']),
            ];

            $browser->visit("/seasons/$seasonId/competitions/")
                ->assertSeeLink('New competition')
                ->with('@list', function (Browser $table) use ($competitions): void {
                    foreach ($competitions as $index => $competition) {
                        /** @var Competition $competition */
                        $table->with("tr:nth-child({$index})", function (Browser $row) use ($competition): void {
                            $row->assertSeeIn('td:nth-child(1)', $competition->getName());
                        });
                    }
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddCompetition(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit("/seasons/1/competitions/create")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $seasonId = factory(Season::class)->create(['year' => 2010])->getId();

            // Check we can add a competitions from the landing page
            $browser->visit("/seasons/$seasonId/competitions")
                ->clickLink('New competition')
                ->assertPathIs("/seasons/$seasonId/competitions/create");

            $browser->visit("/seasons/$seasonId/competitions/create")
                ->assertSee('Add a new competition in the 2010/11 season');

            // Check the form
            $browser->visit("/seasons/$seasonId/competitions/create")
                ->assertInputValue('@season-field', '2010/11')
                ->assertDisabled('@season-field')
                ->assertInputValue('name', '')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'ADD COMPETITION');

            // All fields missing
            $browser->visit("/seasons/$seasonId/competitions/create")
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('ADD COMPETITION')
                ->assertPathIs("/seasons/$seasonId/competitions/create")
                ->assertSeeIn('@name-error', 'The name is required.');

            // Brand new competition
            $browser->visit("/seasons/$seasonId/competitions/create")
                ->type('name', 'London League')
                ->press('ADD COMPETITION')
                ->assertPathIs("/seasons/$seasonId/competitions")
                ->assertSee('Competition added!');

            // Add the same competition
            $browser->visit("/seasons/$seasonId/competitions/create")
                ->type('name', 'London League')
                ->press('ADD COMPETITION')
                ->assertPathIs("/seasons/$seasonId/competitions/create")
                ->assertSeeIn('@name-error', 'The competition already exists in this season.');

            // Add same competition in different season
            $oldSeasonId = factory(Season::class)->create(['year' => 2000])->getId();
            $browser->visit("/seasons/$oldSeasonId/competitions/create")
                ->type('name', 'London Legue')
                ->press('ADD COMPETITION')
                ->assertPathIs("/seasons/$oldSeasonId/competitions")
                ->assertSee('Competition added!');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditCompetition(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit("/seasons/1/competitions/1/edit")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $seasonId = factory(Season::class)->create(['year' => '2010'])->getId();
            $browser->visit("/seasons/$seasonId/competitions/1/edit")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $competitionId = factory(Competition::class)
                ->create(['season_id' => $seasonId, 'name' => 'Youth Games'])
                ->getId();

            $browser->loginAs(factory(User::class)->create());

            // Check we can edit a competitions from the landing page
            $browser->visit("/seasons/$seasonId/competitions")
                ->with('@list', function (Browser $table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs("/seasons/$seasonId/competitions/$competitionId/edit")
                ->assertSee('Edit the Youth Games 2010/11 competition');

            // Check the form
            $browser->visit("/seasons/$seasonId/competitions/$competitionId/edit")
                ->assertInputValue('@season-field', '2010/11')
                ->assertDisabled('@season-field')
                ->assertInputValue('name', 'Youth Games')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // All fields missing
            $browser->visit("/seasons/$seasonId/competitions/$competitionId/edit")
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('SAVE CHANGES')
                ->assertPathIs("/seasons/$seasonId/competitions/$competitionId/edit")
                ->assertSeeIn('@name-error', 'The name is required.');

            $browser->visit("/seasons/$seasonId/competitions/$competitionId/edit")
                ->type('name', 'London League')
                ->press('SAVE CHANGES')
                ->assertPathIs("/seasons/$seasonId/competitions")
                ->assertSee('Competition updated!')
                ->assertSeeIn('@list', 'London League')
                ->assertDontSeeIn('@list', 'Youth Games');

            // Use the name of an already existing competition in this season
            factory(Competition::class)->create(['season_id' => $seasonId, 'name' => 'Mix Volleyball']);
            $browser->visit("/seasons/$seasonId/competitions/$competitionId/edit")
                ->type('name', 'Mix Volleyball')
                ->press('SAVE CHANGES')
                ->assertPathIs("/seasons/$seasonId/competitions/$competitionId/edit")
                ->assertSeeIn('@name-error', 'The competition already exists in this season.');

            // Add same competition in different season
            factory(Competition::class)->create(['name' => 'University Games']);
            $browser->visit("/seasons/$seasonId/competitions/$competitionId/edit")
                ->type('name', 'University Games')
                ->press('SAVE CHANGES')
                ->assertPathIs("/seasons/$seasonId/competitions")
                ->assertSee('Competition updated!')
                ->assertSeeIn('@list', 'University Games');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeleteCompetition(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Competition $competition */
            $competition = factory(Competition::class)->create(['name' => 'Youth Games']);
            $seasonId = $competition->getSeason()->getId();

            $browser->visit("/seasons/$seasonId/competitions/")
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Competition deleted!')
                ->assertSeeIn('@list', 'Youth Games');
            $browser->visit("/seasons/$seasonId/competitions/")
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Competition deleted!')
                ->assertDontSeeIn('@list', 'Youth Games');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testViewDivisions(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Competition $competition */
            $competition = factory(Competition::class)->create();

            factory(Division::class)->times(2)->create(['competition_id' => $competition->getId()]);

            $browser->visit('/seasons/' . $competition->getSeason()->getId() . '/competitions')
                ->with('@list', function (Browser $table): void {
                    $table->clickLink('View');
                })
                ->assertPathIs('/competitions/' . $competition->getId() . '/divisions');
        });
    }
}
