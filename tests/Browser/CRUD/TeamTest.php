<?php

namespace Tests\Browser\CRUD;

use App\Models\Club;
use App\Models\Team;
use App\Models\Venue;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TeamTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListingAllTeamsForNonExistingClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/clubs/1/teams/')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListingAllTeamsForClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $venue = Venue::factory()->create(['name' => 'Sobell SC']);
            $club = Club::factory()->for($venue)->create(['name' => 'Global Warriors']);

            $browser->visit("/clubs/{$club->getId()}/teams/")
                    ->assertSee('Teams in the Global Warriors club')
                    ->assertSeeIn('@list', 'There are no teams in this club yet.');

            Team::factory()->for($club)->for($venue)->create(['name' => 'London Warriors']);
            Team::factory()->for($club)->for($venue)->create(['name' => 'Boston Warriors']);
            Team::factory()->for($club)->create(['name' => 'Cardiff Warriors']);
            Team::factory()->create(['name' => 'London Spiders']);

            $browser->visit("/clubs/{$club->getId()}/teams/")
                    ->assertSeeLink('New team')
                    ->with('@list', function (Browser $table): void {
                        $table->assertSeeIn('thead tr:nth-child(1) th:nth-child(1)', 'Team');
                        $table->assertSeeIn('thead tr:nth-child(1) th:nth-child(2)', 'Venue');

                        $table->assertSeeIn('tbody tr:nth-child(1) td:nth-child(1)', 'Boston Warriors');
                        $table->assertSeeIn('tbody tr:nth-child(1) td:nth-child(2)', 'Sobell SC');
                        $table->assertSeeIn('tbody tr:nth-child(2) td:nth-child(1)', 'Cardiff Warriors');
                        $table->assertSeeIn('tbody tr:nth-child(2) td:nth-child(2)', 'Sobell SC');
                        $table->assertSeeIn('tbody tr:nth-child(3) td:nth-child(1)', 'London Warriors');
                        $table->assertSeeIn('tbody tr:nth-child(3) td:nth-child(2)', 'Sobell SC');
                        $table->assertDontSee('London Spiders');
                    });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddingATeam(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/clubs/1/teams/create')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $venue1 = Venue::factory()->create(['name' => 'Olympic Stadium']);
            $venue2 = Venue::factory()->create(['name' => 'The Box']);
            $club = Club::factory()->for($venue2)->create(['name' => 'Global Warriors']);

            // Check we can add a teams from the landing page
            $browser->visit("/clubs/{$club->getId()}/teams")
                    ->clickLink('New team')
                    ->assertPathIs("/clubs/{$club->getId()}/teams/create");

            $browser->visit("/clubs/{$club->getId()}/teams/create")
                    ->assertSee('Add a new team in the Global Warriors club');

            // Check the form
            $browser->visit("/clubs/{$club->getId()}/teams/create")
                    ->assertInputValue('@club-field', 'Global Warriors')
                    ->assertDisabled('@club-field')
                    ->assertInputValue('name', '')
                    ->assertSelected('@selectVenue-field', '')
                    ->assertSeeIn('@selectVenue-field', "Club's venue (The Box)")
                    ->assertSeeIn('@selectVenue-field', 'Olympic Stadium')
                    ->assertSeeIn('@selectVenue-field', 'The Box')
                    ->assertSelectHasOptions('@selectVenue-field', ['', $venue1->getId(), $venue2->getId()])
                    ->assertVisible('@submit-button')
                    ->assertSeeIn('@submit-button', 'Add team');

            // All fields missing
            $browser->visit("/clubs/{$club->getId()}/teams/create")
                    ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                    ->press('Add team')
                    ->assertPathIs("/clubs/{$club->getId()}/teams/create")
                    ->assertSeeIn('@name-error', 'The name is required.');
            $this->assertDatabaseMissing(
                'teams',
                [
                    'name' => 'Stratford Warriors',
                    'venue_id' => $venue1->getId(),
                ]
            );

            // Brand new team
            $browser->visit("/clubs/{$club->getId()}/teams/create")
                    ->type('name', 'Stratford Warriors')
                    ->select('@selectVenue-field', $venue1->getId())
                    ->press('Add team')
                    ->assertPathIs("/clubs/{$club->getId()}/teams")
                    ->assertSee('Team added!');
            $this->assertDatabaseHas(
                'teams',
                [
                    'name' => 'Stratford Warriors',
                    'venue_id' => $venue1->getId(),
                ]
            );

            // Add the same team (with the same and different venue)
            $browser->visit("/clubs/{$club->getId()}/teams/create")
                    ->type('name', 'Stratford Warriors')
                    ->select('@selectVenue-field', $venue1->getId())
                    ->press('Add team')
                    ->assertPathIs("/clubs/{$club->getId()}/teams/create")
                    ->assertSeeIn('@name-error', 'The team already exists in this club.');
            $browser->visit("/clubs/{$club->getId()}/teams/create")
                    ->type('name', 'Stratford Warriors')
                    ->select('@selectVenue-field', $venue2->getId())
                    ->press('Add team')
                    ->assertPathIs("/clubs/{$club->getId()}/teams/create")
                    ->assertSeeIn('@name-error', 'The team already exists in this club.');

            // Add same team in different club
            $oldClub = Club::factory()->create(['name' => 'Pacific Ladies']);
            $browser->visit("/clubs/{$oldClub->getId()}/teams/create")
                    ->type('name', 'Stratford Warriors')
                    ->press('Add team')
                    ->assertPathIs("/clubs/{$oldClub->getId()}/teams")
                    ->assertSee('Team added!');
            $this->assertDatabaseHas(
                'teams',
                [
                    'name' => 'Stratford Warriors',
                    'club_id' => $club->getId(),
                ]
            );
            $this->assertDatabaseHas(
                'teams',
                [
                    'name' => 'Stratford Warriors',
                    'club_id' => $oldClub->getId(),
                ]
            );
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditingATeam(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit('/clubs/1/teams/1/edit')
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $venue1 = Venue::factory()->create(['name' => 'Olympic Stadium']);
            $theBox = Venue::factory()->create(['name' => 'The Box']);
            $club = Club::factory()->for($theBox)->create(['name' => 'Global Warriors']);

            $browser->visit("/clubs/{$club->getId()}/teams/1/edit")
                    ->assertTitle('Not Found')
                    ->assertSee('404')
                    ->assertSee('NOT FOUND');

            $team = Team::factory()->for($club)->create(['name' => 'London Warriors']);

            // Check we can edit a team from the landing page
            $browser->visit("/clubs/{$club->getId()}/teams")
                    ->with("@team-{$team->getId()}-row", function (Browser $table) {
                        $table->clickLink('Update');
                    })
                    ->assertPathIs("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
                    ->assertSee('Edit the London Warriors (Global Warriors) team');

            // Check the form
            $browser->visit("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
                    ->assertInputValue('@club-field', 'Global Warriors')
                    ->assertDisabled('@club-field')
                    ->assertInputValue('name', 'London Warriors')
                    ->assertSelected('@selectVenue-field', '')
                    ->assertSeeIn('@selectVenue-field', "Club's venue (The Box)")
                    ->assertSeeIn('@selectVenue-field', 'Olympic Stadium')
                    ->assertSeeIn('@selectVenue-field', 'The Box')
                    ->assertSelectHasOptions('@selectVenue-field', ['', $venue1->getId(), $theBox->getId()])
                    ->assertVisible('@submit-button')
                    ->assertSeeIn('@submit-button', 'Save changes');

            // All fields missing
            $browser->visit("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
                    ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                    ->press('Save changes')
                    ->assertPathIs("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
                    ->assertSeeIn('@name-error', 'The name is required.');
            $this->assertDatabaseHas(
                'teams',
                [
                    'id' => $team->getId(),
                    'name' => 'London Warriors',
                    'venue_id' => null,
                ]
            );

            $browser->visit("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
                    ->type('name', 'Boston Warriors')
                    ->press('Save changes')
                    ->assertPathIs("/clubs/{$club->getId()}/teams")
                    ->assertSee('Team updated!');
            $this->assertDatabaseHas(
                'teams',
                [
                    'id' => $team->getId(),
                    'name' => 'Boston Warriors',
                    'venue_id' => null,
                ]
            );

            $browser->visit("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
                    ->select('@selectVenue-field', $venue1->getId())
                    ->press('Save changes')
                    ->assertPathIs("/clubs/{$club->getId()}/teams")
                    ->assertSee('Team updated!');
            $this->assertDatabaseHas(
                'teams',
                [
                    'id' => $team->getId(),
                    'name' => 'Boston Warriors',
                    'venue_id' => $venue1->getId(),
                ]
            );

            // Use the name of an already existing team in this club
            Team::factory()->for($club)->create(['name' => 'Cardiff Warriors']);
            $browser->visit("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
                    ->type('name', 'Cardiff Warriors')
                    ->press('Save changes')
                    ->assertPathIs("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
                    ->assertSeeIn('@name-error', 'The team already exists in this club.');
            $this->assertDatabaseHas(
                'teams',
                [
                    'id' => $team->getId(),
                    'name' => 'Boston Warriors',
                    'venue_id' => $venue1->getId(),
                ]
            );

            // Use same team in different club
            Team::factory()->create(['name' => 'Manchester Warriors']);
            $browser->visit("/clubs/{$club->getId()}/teams/{$team->getId()}/edit")
                    ->type('name', 'Manchester Warriors')
                    ->press('Save changes')
                    ->assertPathIs("/clubs/{$club->getId()}/teams")
                    ->assertSee('Team updated!');
            $this->assertDatabaseHas(
                'teams',
                [
                    'id' => $team->getId(),
                    'name' => 'Manchester Warriors',
                    'venue_id' => $venue1->getId(),
                ]
            );
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeletingATeam(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $club = club::factory()->create();
            $team = Team::factory()->for($club)->create(['name' => 'London Warriors']);

            $browser->visit("/clubs/{$club->getId()}/teams/")
                    ->within("@team-{$team->getId()}-row", function (Browser $row): void {
                        $row->press('Delete');
                    })
                    ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                        $modal->assertSee('Are you sure?')
                              ->press('Cancel')
                              ->pause(1000);
                    })
                    ->assertDontSee('Team deleted!');
            $this->assertDatabaseHas('teams', ['id' => $team->getId()]);

            $browser->visit("/clubs/{$club->getId()}/teams/")
                    ->within("@team-{$team->getId()}-row", function (Browser $row): void {
                        $row->press('Delete');
                    })
                    ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                        $modal->assertSee('Are you sure?')
                              ->press('Confirm')
                              ->pause(1000);
                    })
                    ->assertSee('Team deleted!');
            $this->assertDatabaseMissing('teams', ['id' => $team->getId()]);
        });
    }
}
