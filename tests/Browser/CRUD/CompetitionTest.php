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
    public function testListingAllCompetitionsForNonExistingSeason(): void
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
    public function testListingAllCompetitionsForSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $seasonId = factory(Season::class)->create(['year' => 2001])->getId();

            $browser->visit("/seasons/$seasonId/competitions/")
                ->assertSee("Competitions in the 2001/02 season")
                ->assertSeeIn('@list', 'There are no competitions in this season yet.');

            factory(Competition::class)->create(['name' => 'University Challenge']);
            factory(Competition::class)->create(['season_id' => $seasonId, 'name' => 'London League']);
            factory(Competition::class)->create(['season_id' => $seasonId, 'name' => 'Youth Games']);
            factory(Competition::class)->create(['season_id' => $seasonId, 'name' => 'Super8']);

            $browser->visit("/seasons/$seasonId/competitions/")
                ->assertSeeLink('New competition')
                ->with('@list', function (Browser $table): void {
                    $table->assertSeeIn('thead tr:nth-child(1)', 'Competition');

                    $table->assertSeeIn('tbody tr:nth-child(1)', 'London League');
                    $table->assertSeeIn('tbody tr:nth-child(2)', 'Super8');
                    $table->assertSeeIn('tbody tr:nth-child(3)', 'Youth Games');
                    $table->assertDontSee('Univesity Challenge');
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddingACompetition(): void
    {
        $this->browse(function (Browser $browser): void {
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
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $seasonId]);

            // Add the same competition
            $browser->visit("/seasons/$seasonId/competitions/create")
                ->type('name', 'London League')
                ->press('ADD COMPETITION')
                ->assertPathIs("/seasons/$seasonId/competitions/create")
                ->assertSeeIn('@name-error', 'The competition already exists in this season.');

            // Add same competition in different season
            $oldSeasonId = factory(Season::class)->create(['year' => 2000])->getId();
            $browser->visit("/seasons/$oldSeasonId/competitions/create")
                ->type('name', 'London League')
                ->press('ADD COMPETITION')
                ->assertPathIs("/seasons/$oldSeasonId/competitions")
                ->assertSee('Competition added!');
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $seasonId]);
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $oldSeasonId]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditingACompetition(): void
    {
        $this->browse(function (Browser $browser): void {
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
                ->create(['name' => 'Youth Games', 'season_id' => $seasonId])
                ->getId();

            $browser->loginAs(factory(User::class)->create());

            // Check we can edit a competitions from the landing page
            $browser->visit("/seasons/$seasonId/competitions")
                ->within("@competition-$competitionId-row", function (Browser $row): void {
                    $row->clickLink('Update');
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
            $this->assertDatabaseHas('competitions', ['name' => 'Youth Games', 'season_id' => $seasonId]);

            $browser->visit("/seasons/$seasonId/competitions/$competitionId/edit")
                ->type('name', 'London League')
                ->press('SAVE CHANGES')
                ->assertPathIs("/seasons/$seasonId/competitions")
                ->assertSee('Competition updated!');
            $this->assertDatabaseMissing('competitions', ['name' => 'Youth Games', 'season_id' => $seasonId]);
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $seasonId]);

            // Use the name of an already existing competition in this season
            factory(Competition::class)->create(['name' => 'Mix Volleyball', 'season_id' => $seasonId]);
            $browser->visit("/seasons/$seasonId/competitions/$competitionId/edit")
                ->type('name', 'Mix Volleyball')
                ->press('SAVE CHANGES')
                ->assertPathIs("/seasons/$seasonId/competitions/$competitionId/edit")
                ->assertSeeIn('@name-error', 'The competition already exists in this season.');
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $seasonId]);

            // Add same competition in different season
            factory(Competition::class)->create(['name' => 'University Games']);
            $browser->visit("/seasons/$seasonId/competitions/$competitionId/edit")
                ->type('name', 'University Games')
                ->press('SAVE CHANGES')
                ->assertPathIs("/seasons/$seasonId/competitions")
                ->assertSee('Competition updated!');
            $this->assertDatabaseHas('competitions', ['name' => 'University Games', 'season_id' => $seasonId]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeletingACompetition(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $seasonId = factory(Season::class)->create()->getId();
            $competitionId= factory(Competition::class)->create(['season_id' => $seasonId])->getId();

            $browser->visit("/seasons/$seasonId/competitions/")
                ->within("@competition-$competitionId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Competition deleted!');
            $this->assertDatabaseHas('competitions', ['id' => $competitionId]);

            $browser->visit("/seasons/$seasonId/competitions/")
                ->within("@competition-$competitionId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Competition deleted!');
            $this->assertDatabaseMissing('competitions', ['id' => $competitionId]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testViewingTheCompetitionDivisions(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $seasonId = factory(Season::class)->create()->getId();
            $competitionId = factory(Competition::class)
                ->create(['name' => 'Youth Games', 'season_id' => $seasonId])
                ->getId();

            factory(Division::class)->create(['name' => 'DIV1M', 'competition_id' => $competitionId]);
            factory(Division::class)->create(['name' => 'DIV1W']);

            $browser->visit("/seasons/$seasonId/competitions")
                ->within("@competition-$competitionId-row", function (Browser $row): void {
                    $row->clickLink('View');
                })
                ->assertPathIs("/competitions/$competitionId/divisions")
                ->assertSeeIn('@list', 'DIV1M')
                ->assertDontSeeIn('@list', 'DIV1W');
        });
    }
}
