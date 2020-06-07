<?php

namespace Tests\Browser\CRUD;

use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Collection;
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

            $browser->visit("/clubs/1/teams/")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListingAllTeamsForClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $venue = factory(Venue::class)->create(['name' => 'Sobell SC']);
            $club = aClub()->withVenue($venue)->withName('Global Warriors')->build();

            $browser->visit("/clubs/{$club->getId()}/teams/")
                ->assertSee("Teams in the Global Warriors club")
                ->assertSeeIn('@list', 'There are no teams in this club yet.');

            aTeam()->inClub($club)->withVenue($venue)->withName('London Warriors')->build();
            aTeam()->inClub($club)->withVenue($venue)->withName('Boston Warriors')->build();
            aTeam()->inClub($club)->withName('Cardiff Warriors')->build();
            aTeam()->withName('London Spiders')->build();

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

            $browser->visit("/clubs/1/teams/create")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $venue1 = factory(Venue::class)->create(['name' => 'Olympic Stadium']);
            $venue2 = factory(Venue::class)->create(['name' => 'The Box']);
            $clubId = aClub()
                ->withName('Global Warriors')
                ->withVenue($venue2)
                ->build()
                ->getId();

            // Check we can add a teams from the landing page
            $browser->visit("/clubs/$clubId/teams")
                ->clickLink('New team')
                ->assertPathIs("/clubs/$clubId/teams/create");

            $browser->visit("/clubs/$clubId/teams/create")
                ->assertSee('Add a new team in the Global Warriors club');

            // Check the form
            $browser->visit("/clubs/$clubId/teams/create")
                ->assertInputValue('@club-field', 'Global Warriors')
                ->assertDisabled('@club-field')
                ->assertInputValue('name', '')
                ->assertSelected('@selectVenue-field', '')
                ->assertSeeIn('@selectVenue-field', "Club's venue (The Box)")
                ->assertSeeIn('@selectVenue-field', 'Olympic Stadium')
                ->assertSeeIn('@selectVenue-field', 'The Box')
                ->assertSelectHasOptions('@selectVenue-field', ["", $venue1->getId(), $venue2->getId()])
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'Add team');

            // All fields missing
            $browser->visit("/clubs/$clubId/teams/create")
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('Add team')
                ->assertPathIs("/clubs/$clubId/teams/create")
                ->assertSeeIn('@name-error', 'The name is required.');
            $this->assertDatabaseMissing('teams', [
                'name'     => 'Stratford Warriors',
                'venue_id' => $venue1->getId(),
            ]);

            // Brand new team
            $browser->visit("/clubs/$clubId/teams/create")
                ->type('name', 'Stratford Warriors')
                ->select('@selectVenue-field', $venue1->getId())
                ->press('Add team')
                ->assertPathIs("/clubs/$clubId/teams")
                ->assertSee('Team added!');
            $this->assertDatabaseHas('teams', [
                'name'     => 'Stratford Warriors',
                'venue_id' => $venue1->getId(),
            ]);

            // Add the same team (with the same and different venue)
            $browser->visit("/clubs/$clubId/teams/create")
                ->type('name', 'Stratford Warriors')
                ->select('@selectVenue-field', $venue1->getId())
                ->press('Add team')
                ->assertPathIs("/clubs/$clubId/teams/create")
                ->assertSeeIn('@name-error', 'The team already exists in this club.');
            $browser->visit("/clubs/$clubId/teams/create")
                ->type('name', 'Stratford Warriors')
                ->select('@selectVenue-field', $venue2->getId())
                ->press('Add team')
                ->assertPathIs("/clubs/$clubId/teams/create")
                ->assertSeeIn('@name-error', 'The team already exists in this club.');

            // Add same team in different club
            $oldClubId = aClub()->withName('Pacific Ladies')->build()->getId();
            $browser->visit("/clubs/$oldClubId/teams/create")
                ->type('name', 'Stratford Warriors')
                ->press('Add team')
                ->assertPathIs("/clubs/$oldClubId/teams")
                ->assertSee('Team added!');
            $this->assertDatabaseHas('teams', [
                'name'    => 'Stratford Warriors',
                'club_id' => $clubId,
            ]);
            $this->assertDatabaseHas('teams', [
                'name'    => 'Stratford Warriors',
                'club_id' => $oldClubId,
            ]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditingATeam(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $browser->visit("/clubs/1/teams/1/edit")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $venue1 = factory(Venue::class)->create(['name' => 'Olympic Stadium']);
            $theBox = factory(Venue::class)->create(['name' => 'The Box']);
            $club = aClub()->withName('Global Warriors')->withVenue($theBox)->build();
            $clubId = $club->getId();

            $browser->visit("/clubs/$clubId/teams/1/edit")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $team = aTeam()->withName('London Warriors')->inClub($club)->build();
            $teamId = $team->getId();

            // Check we can edit a team from the landing page
            $browser->visit("/clubs/$clubId/teams")
                ->with("@team-$teamId-row", function (Browser $table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs("/clubs/$clubId/teams/$teamId/edit")
                ->assertSee('Edit the London Warriors (Global Warriors) team');

            // Check the form
            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->assertInputValue('@club-field', 'Global Warriors')
                ->assertDisabled('@club-field')
                ->assertInputValue('name', 'London Warriors')
                ->assertSelected('@selectVenue-field', '')
                ->assertSeeIn('@selectVenue-field', "Club's venue (The Box)")
                ->assertSeeIn('@selectVenue-field', 'Olympic Stadium')
                ->assertSeeIn('@selectVenue-field', 'The Box')
                ->assertSelectHasOptions('@selectVenue-field', ["", $venue1->getId(), $theBox->getId()])
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'Save changes');

            // All fields missing
            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/teams/$teamId/edit")
                ->assertSeeIn('@name-error', 'The name is required.');
            $this->assertDatabaseHas('teams', [
                'id'       => $teamId,
                'name'     => 'London Warriors',
                'venue_id' => null,
            ]);

            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->type('name', 'Boston Warriors')
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/teams")
                ->assertSee('Team updated!');
            $this->assertDatabaseHas('teams', [
                'id'       => $teamId,
                'name'     => 'Boston Warriors',
                'venue_id' => null,
            ]);

            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->select('@selectVenue-field', $venue1->getId())
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/teams")
                ->assertSee('Team updated!');
            $this->assertDatabaseHas('teams', [
                'id'       => $teamId,
                'name'     => 'Boston Warriors',
                'venue_id' => $venue1->getId(),
            ]);

            // Use the name of an already existing team in this club
            aTeam()->withName('Cardiff Warriors')->inClub($club)->build();
            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->type('name', 'Cardiff Warriors')
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/teams/$teamId/edit")
                ->assertSeeIn('@name-error', 'The team already exists in this club.');
            $this->assertDatabaseHas('teams', [
                'id'       => $teamId,
                'name'     => 'Boston Warriors',
                'venue_id' => $venue1->getId(),
            ]);

            // Use same team in different club
            aTeam()->withName('Manchester Warriors')->build();
            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->type('name', 'Manchester Warriors')
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/teams")
                ->assertSee('Team updated!');
            $this->assertDatabaseHas('teams', [
                'id'       => $teamId,
                'name'     => 'Manchester Warriors',
                'venue_id' => $venue1->getId(),
            ]);
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeletingATeam(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs($this->siteAdmin);

            $club = aClub()->build();
            $teamId = aTeam()->withName('London Warriors')->inClub($club)->build()->getId();

            $browser->visit("/clubs/{$club->getId()}/teams/")
                ->within("@team-$teamId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Team deleted!');
            $this->assertDatabaseHas('teams', ['id' => $teamId]);

            $browser->visit("/clubs/{$club->getId()}/teams/")
                ->within("@team-$teamId-row", function (Browser $row): void {
                    $row->press('Delete');
                })
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Team deleted!');
            $this->assertDatabaseMissing('teams', ['id' => $teamId]);
        });
    }
}
