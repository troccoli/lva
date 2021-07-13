<?php

namespace Tests\Browser\CRUD;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
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
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/seasons/1/competitions/')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListingAllCompetitionsForSeason(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $season2001 = Season::factory()->create(['year' => 2001]);
            $season2002 = Season::factory()->create(['year' => 2002]);

            $browser->visit("/seasons/{$season2001->getId()}/competitions/")
                    ->assertSee('Competitions in the 2001/02 season')
                    ->assertSeeIn('@list', 'There are no competitions in this season yet.');

            Competition::factory()->for($season2002)->create(['name' => 'University Challenge']);
            Competition::factory()->for($season2001)->create(['name' => 'London League']);
            Competition::factory()->for($season2001)->create(['name' => 'Youth Games']);
            Competition::factory()->for($season2001)->create(['name' => 'Super8']);

            $browser->visit("/seasons/{$season2001->getId()}/competitions/")
                    ->assertSeeLink('New competition')
                    ->with('@list', function (Browser $table): void {
                        $table->assertSeeIn('thead tr:nth-child(1)', 'Competition');

                        $table->assertSeeIn('tbody tr:nth-child(1)', 'London League');
                        $table->assertSeeIn('tbody tr:nth-child(2)', 'Super8');
                        $table->assertSeeIn('tbody tr:nth-child(3)', 'Youth Games');
                        $table->assertDontSee('University Challenge');
                    });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddingACompetition(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/seasons/1/competitions/create')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $season = Season::factory()->create(['year' => 2010]);

            // Check we can add a competitions from the landing page
            $browser->visit("/seasons/{$season->getId()}/competitions")
                    ->clickLink('New competition')
                    ->assertPathIs("/seasons/{$season->getId()}/competitions/create");

            $browser->visit("/seasons/{$season->getId()}/competitions/create")
                    ->assertSee('Add a new competition in the 2010/11 season');

            // Check the form
            $browser->visit("/seasons/{$season->getId()}/competitions/create")
                    ->assertInputValue('@season-field', '2010/11')
                    ->assertDisabled('@season-field')
                    ->assertInputValue('name', '')
                    ->assertVisible('@submit-button')
                    ->assertSeeIn('@submit-button', 'ADD COMPETITION');

            // All fields missing
            $browser->visit("/seasons/{$season->getId()}/competitions/create")
                    ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                    ->press('ADD COMPETITION')
                    ->assertPathIs("/seasons/{$season->getId()}/competitions/create")
                    ->assertSeeIn('@name-error', 'The name is required.');

            // Brand new competition
            $browser->visit("/seasons/{$season->getId()}/competitions/create")
                    ->type('name', 'London League')
                    ->press('ADD COMPETITION')
                    ->assertPathIs("/seasons/{$season->getId()}/competitions")
                    ->assertSee('Competition added!');
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $season->getId()]);

            // Add the same competition
            $browser->visit("/seasons/{$season->getId()}/competitions/create")
                    ->type('name', 'London League')
                    ->press('ADD COMPETITION')
                    ->assertPathIs("/seasons/{$season->getId()}/competitions/create")
                    ->assertSeeIn('@name-error', 'The competition already exists in this season.');

            // Add same competition in different season
            $oldSeason = Season::factory()->create(['year' => 2000]);
            $browser->visit("/seasons/{$oldSeason->getId()}/competitions/create")
                    ->type('name', 'London League')
                    ->press('ADD COMPETITION')
                    ->assertPathIs("/seasons/{$oldSeason->getId()}/competitions")
                    ->assertSee('Competition added!');
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $season->getId()]);
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $oldSeason->getId()]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditingACompetition(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/seasons/1/competitions/1/edit')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $season = Season::factory()->create(['year' => '2010']);
            $browser->visit("/seasons/{$season->getId()}/competitions/1/edit")
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $competition = Competition::factory()->for($season)->create(['name' => 'Youth Games']);

            // Check we can edit a competitions from the landing page
            $browser->visit("/seasons/{$season->getId()}/competitions")
                    ->within("@competition-{$competition->getId()}-row", function (Browser $row): void {
                        $row->clickLink('Update');
                    })
                    ->assertPathIs("/seasons/{$season->getId()}/competitions/{$competition->getId()}/edit")
                    ->assertSee('Edit the Youth Games 2010/11 competition');

            // Check the form
            $browser->visit("/seasons/{$season->getId()}/competitions/{$competition->getId()}/edit")
                    ->assertInputValue('@season-field', '2010/11')
                    ->assertDisabled('@season-field')
                    ->assertInputValue('name', 'Youth Games')
                    ->assertVisible('@submit-button')
                    ->assertSeeIn('@submit-button', 'SAVE CHANGES');

            // All fields missing
            $browser->visit("/seasons/{$season->getId()}/competitions/{$competition->getId()}/edit")
                    ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/seasons/{$season->getId()}/competitions/{$competition->getId()}/edit")
                    ->assertSeeIn('@name-error', 'The name is required.');
            $this->assertDatabaseHas('competitions', ['name' => 'Youth Games', 'season_id' => $season->getId()]);

            $browser->visit("/seasons/{$season->getId()}/competitions/{$competition->getId()}/edit")
                    ->type('name', 'London League')
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/seasons/{$season->getId()}/competitions")
                    ->assertSee('Competition updated!');
            $this->assertDatabaseMissing('competitions', ['name' => 'Youth Games', 'season_id' => $season->getId()]);
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $season->getId()]);

            // Use the name of an already existing competition in this season
            Competition::factory()->for($season)->create(['name' => 'Mix Volleyball']);
            $browser->visit("/seasons/{$season->getId()}/competitions/{$competition->getId()}/edit")
                    ->type('name', 'Mix Volleyball')
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/seasons/{$season->getId()}/competitions/{$competition->getId()}/edit")
                    ->assertSeeIn('@name-error', 'The competition already exists in this season.');
            $this->assertDatabaseHas('competitions', ['name' => 'London League', 'season_id' => $season->getId()]);

            // Add same competition in different season
            $anotherSeason = Season::factory()->create(['year' => 1999]);
            Competition::factory()->for($anotherSeason)->create(['name' => 'University Games']);
            $browser->visit("/seasons/{$season->getId()}/competitions/{$competition->getId()}/edit")
                    ->type('name', 'University Games')
                    ->press('SAVE CHANGES')
                    ->assertPathIs("/seasons/{$season->getId()}/competitions")
                    ->assertSee('Competition updated!');
            $this->assertDatabaseHas('competitions', ['name' => 'University Games', 'season_id' => $season->getId()]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeletingACompetition(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $season = Season::factory()->create();
            $competition = Competition::factory()->for($season)->create();

            $browser->visit("/seasons/{$season->getId()}/competitions/")
                    ->within("@competition-{$competition->getId()}-row", function (Browser $row): void {
                        $row->press('Delete');
                    })
                    ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                        $modal->assertSee('Are you sure?')
                              ->press('Cancel')
                              ->pause(1000);
                    })
                    ->assertDontSee('Competition deleted!');
            $this->assertDatabaseHas('competitions', ['id' => $competition->getId()]);

            $browser->visit("/seasons/{$season->getId()}/competitions/")
                    ->within("@competition-{$competition->getId()}-row", function (Browser $row): void {
                        $row->press('Delete');
                    })
                    ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                        $modal->assertSee('Are you sure?')
                              ->press('Confirm')
                              ->pause(1000);
                    })
                    ->assertSee('Competition deleted!');
            $this->assertDatabaseMissing('competitions', ['id' => $competition->getId()]);
        }
        );
    }

    /**
     * @throws \Throwable
     */
    public function testViewingTheCompetitionDivisions(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $season = Season::factory()->create();
            $competition = Competition::factory()->for($season)->create(['name' => 'Youth Games']);

            Division::factory()->for($competition)->create(['name' => 'DIV1M']);
            Division::factory()->create(['name' => 'DIV1W']);

            $browser->visit("/seasons/{$season->getId()}/competitions")
                    ->within("@competition-{$competition->getId()}-row", function (Browser $row): void {
                        $row->clickLink('View');
                    })
                    ->assertPathIs("/competitions/{$competition->getId()}/divisions")
                    ->assertSeeIn('@list', 'DIV1M')
                    ->assertDontSeeIn('@list', 'DIV1W');
        });
    }
}
