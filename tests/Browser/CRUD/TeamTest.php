<?php

namespace Tests\Browser\CRUD;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TeamTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListAllTeamsForNonExistingClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit("/clubs/1/teams/")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testListAllTeamsForClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $club = aClub()->withName('Global Warriors')->build();

            $browser->loginAs(factory(User::class)->create());

            $browser->visit("/clubs/{$club->getId()}/teams/")
                ->assertSee("Teams in the Global Warriors club")
                ->assertSeeIn('@list', 'There are no teams in this club yet.');

            $otherTeams = aTeam()->build(5);
            /** @var Collection $teams */
            $teams = aTeam()->inClub($club)->orderedByName()->build(3);

            $browser->visit("/clubs/{$club->getId()}/teams/")
                ->assertSeeLink('New team')
                ->with('@list', function (Browser $table) use ($teams): void {
                    $row = 1;
                    foreach ($teams as $team) {
                        /** @var Team $team */
                        $table->with("tr:nth-child({$row})", function (Browser $row) use ($team): void {
                            $row->assertSeeIn('td:nth-child(1)', $team->getName());
                        });
                        $row++;
                    }
                })
                ->with('@list', function (Browser $table) use ($otherTeams): void {
                    foreach ($otherTeams as $team) {
                        /** @var Team $team */
                        $table->assertDontSee($team->getName());
                    };
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddTeam(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit("/clubs/1/teams/create")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $clubId = aClub()->withName('Global Warriors')->build()->getId();

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
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'Add team');

            // All fields missing
            $browser->visit("/clubs/$clubId/teams/create")
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('Add team')
                ->assertPathIs("/clubs/$clubId/teams/create")
                ->assertSeeIn('@name-error', 'The name is required.');

            // Brand new team
            $browser->visit("/clubs/$clubId/teams/create")
                ->type('name', 'London Warriors')
                ->press('Add team')
                ->assertPathIs("/clubs/$clubId/teams")
                ->assertSee('Team added!');

            // Add the same team
            $browser->visit("/clubs/$clubId/teams/create")
                ->type('name', 'London Warriors')
                ->press('Add team')
                ->assertPathIs("/clubs/$clubId/teams/create")
                ->assertSeeIn('@name-error', 'The team already exists in this club.');

            // Add same team in different club
            $oldClubId = aClub()->withName('Pacific Ladies')->build()->getId();
            $browser->visit("/clubs/$oldClubId/teams/create")
                ->type('name', 'London Warriors')
                ->press('Add team')
                ->assertPathIs("/clubs/$oldClubId/teams")
                ->assertSee('Team added!');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditTeam(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit("/clubs/1/teams/1/edit")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $club = aClub()->withName('Global Warriors')->build();
            $clubId = $club->getId();
            $browser->visit("/clubs/$clubId/teams/1/edit")
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found');

            $team = aTeam()->withName('London Warriors')->inClub($club)->build();
            $teamId = $team->getId();

            $browser->loginAs(factory(User::class)->create());

            // Check we can edit a teams from the landing page
            $browser->visit("/clubs/$clubId/teams")
                ->with('@list', function (Browser $table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs("/clubs/$clubId/teams/$teamId/edit")
                ->assertSee('Edit the London Warriors (Global Warriors) team');

            // Check the form
            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->assertInputValue('@club-field', 'Global Warriors')
                ->assertDisabled('@club-field')
                ->assertInputValue('name', 'London Warriors')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'Save changes');

            // All fields missing
            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/teams/$teamId/edit")
                ->assertSeeIn('@name-error', 'The name is required.');

            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->type('name', 'Boston Warriors')
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/teams")
                ->assertSee('Team updated!')
                ->assertSeeIn('@list', 'Boston Warriors')
                ->assertDontSeeIn('@list', 'London Warriors');

            // Use the name of an already existing team in this club
            aTeam()->withName('Cardiff Warriors')->inClub($club)->build();
            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->type('name', 'Cardiff Warriors')
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/teams/$teamId/edit")
                ->assertSeeIn('@name-error', 'The team already exists in this club.');

            // Add same team in different club
            aTeam()->withName('Manchester Warriors')->build();
            $browser->visit("/clubs/$clubId/teams/$teamId/edit")
                ->type('name', 'Manchester Warriors')
                ->press('Save changes')
                ->assertPathIs("/clubs/$clubId/teams")
                ->assertSee('Team updated!')
                ->assertSeeIn('@list', 'Manchester Warriors');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeleteTeam(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $club = aClub()->build();
            /** @var Team $team */
            $team = aTeam()->withName('London Warriors')->inClub($club)->build();

            $browser->visit("/clubs/{$club->getId()}/teams/")
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Team deleted!')
                ->assertSeeIn('@list', 'London Warriors');
            $browser->visit("/clubs/{$club->getId()}/teams/")
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Team deleted!')
                ->assertDontSeeIn('@list', 'London Warriors');
        });
    }
}
