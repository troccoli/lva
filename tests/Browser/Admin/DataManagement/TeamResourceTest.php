<?php

namespace Tests\Browser\Admin\DataManagement;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Dusk\Browser;
use LVA\Models\Club;
use LVA\Models\Division;
use LVA\Models\Fixture;
use LVA\Models\Team;
use LVA\User;
use Tests\Browser\Pages\Resources\TeamsPage;
use Tests\DuskTestCase;

class TeamResourceTest extends DuskTestCase
{
    public function testRedirectIfNotAdmin()
    {
        $page = new TeamsPage();

        $this->browse(function (Browser $browser) use ($page) {
            $team = factory(Team::class)->create();

            $browser->visit($page->indexUrl())
                ->assertRouteIs('login');

            $browser->visit($page->createUrl())
                ->assertRouteIs('login');

            $browser->visit($page->showUrl($team->id))
                ->assertRouteIs('login');

            $browser->visit($page->editUrl($team->id))
                ->assertRouteIs('login');

        });
    }

    public function testListTeams()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Collection $teams */
            $teams = factory(Team::class)->times(20)->create();

            $page1 = $teams->slice(0, 15);
            $page2 = $teams->slice(15, 5);

            $page = new TeamsPage();
            $browser->visit($page)
                ->assertSeeIn($page->breadcrumb, 'Teams')
                ->assertSeeLink('New team')
                ->with('tbody', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $team) {
                        $table->with("tr:nth-child($child)", function ($row) use ($team) {
                            $linkText = $team->team;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $team->club->club)
                                ->assertSeeIn('td:nth-child(2)', $linkText)
                                ->assertSeeIn('td:nth-child(3)', $team->trigram);
                        });
                        $child++;
                    }
                })
                ->with($page->pageNavigation, function ($nav) {
                    $nav->clickLink(2);
                })
                ->assertPathIs($page->indexUrl())
                ->with('tbody', function ($table) use ($page2) {
                    $child = 1;
                    foreach ($page2 as $team) {
                        $table->with("tr:nth-child($child)", function ($row) use ($team) {
                            $linkText = $team->team;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $team->club->club)
                                ->assertSeeIn('td:nth-child(2)', $linkText)
                                ->assertSeeIn('td:nth-child(3)', $team->trigram);
                        });
                        $child++;
                    }
                });
        });
    }

    public function testAddTeam()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $page = new TeamsPage();

            // Check we can add a team from the landing page
            $browser->visit($page)
                ->clickLink('New team')
                ->assertPathIs($page->createUrl());

            /** @var Team $team */
            $team = factory(Team::class)->make();

            // Brand new team
            $browser->visit($page->createUrl())
                ->select('club_id', $team->club_id)
                ->type('team', $team->team)
                ->type('trigram', $team->trigram)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Team added!');

            // Already existing team in the same club
            $browser->visit($page->createUrl())
                ->select('club_id', $team->club_id)
                ->type('team', $team->team)
                ->type('trigram', $team->trigram)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@team-error', 'The team already exists in the same club.')
                ->assertVisible('@form-errors');

            // Already assigned trigram
            /** @var Team $team */
            $newTeam = factory(Team::class)->make();
            $browser->visit($page->createUrl())
                ->select('club_id', $newTeam->club_id)
                ->type('team', $newTeam->team)
                ->type('trigram', $team->trigram)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@trigram-error', 'The trigram has already been taken.')
                ->assertVisible('@form-errors');

            // Non numeric nor string trigram
            $trigram = $this->faker->regexify('[!@$%&_=-;,./><":{}]{3}');
            $browser->visit($page->createUrl())
                ->select('club_id', $newTeam->club_id)
                ->type('team', $newTeam->team)
                ->type('trigram', $trigram)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@trigram-error', 'The trigram may only contain letters and numbers.')
                ->assertVisible('@form-errors');

            // Too short trigram
            $trigram = $this->faker->regexify('[A-Z]{2}');
            $browser->visit($page->createUrl())
                ->select('club_id', $newTeam->club_id)
                ->type('team', $newTeam->team)
                ->type('trigram', $trigram)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@trigram-error', 'The trigram must be 3 characters.')
                ->assertVisible('@form-errors');

            // Too long trigram
            $trigram = $this->faker->regexify('[A-Z]{4}');
            $browser->visit($page->createUrl())
                ->select('club_id', $newTeam->club_id)
                ->type('team', $newTeam->team)
                ->type('trigram', $trigram)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@trigram-error', 'The trigram must be 3 characters.')
                ->assertVisible('@form-errors');

            // Missing fields
            $browser->visit($page->createUrl())
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@team-error', 'The team field is required.')
                ->assertSeeIn('@trigram-error', 'The trigram field is required.')
                ->assertVisible('@form-errors');
        });
    }

    public function testEditTeam()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Team $team */
            $team = factory(Team::class)->create();

            $page = new TeamsPage();

            // Check we can edit a team from the landing page
            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs($page->editUrl($team->id));

            // Don't change anything
            $browser->visit($page->editUrl($team->id))
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Team updated!');

            /** @var Team $newTeam */
            $newTeam = factory(Team::class)->make();

            // Change the name of the team
            $browser->visit($page->editUrl($team->id))
                ->type('team', $newTeam->team)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Team updated!');

            // Change the trigram of the team
            $browser->visit($page->editUrl($team->id))
                ->type('trigram', $newTeam->trigram)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Team updated!');

            // Already existing team in the same club
            /** @var Team $newTeam */
            $newTeam = factory(Team::class)->create(['club_id' => $team->club_id]);
            $browser->visit($page->editUrl($team->id))
                ->type('team', $newTeam->team)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($team->id))
                ->assertSeeIn('@team-error', 'The team already exists in the same club.')
                ->assertVisible('@form-errors');

            // Move team to a different club
            $club = factory(Club::class)->create();
            $browser->visit($page->editUrl($team->id))
                ->select('club_id', $club->id)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Team updated!');

            // Already assigned trigram
            /** @var Team $newTeam */
            $newTeam = factory(Team::class)->create();
            $browser->visit($page->editUrl($team->id))
                ->type('trigram', $newTeam->trigram)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($team->id))
                ->assertSeeIn('@trigram-error', 'The trigram has already been taken.')
                ->assertVisible('@form-errors');

            // Non numeric nor string trigram
            $trigram = $this->faker->regexify('[!@$%&_=-;,./><":{}]{3}');
            $browser->visit($page->editUrl($team->id))
                ->type('trigram', $trigram)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($team->id))
                ->assertSeeIn('@trigram-error', 'The trigram may only contain letters and numbers.')
                ->assertVisible('@form-errors');

            // Too short trigram
            $trigram = $this->faker->regexify('[A-Z]{2}');
            $browser->visit($page->editUrl($team->id))
                ->type('trigram', $trigram)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($team->id))
                ->assertSeeIn('@trigram-error', 'The trigram must be 3 characters.')
                ->assertVisible('@form-errors');

            // Too long trigram
            $trigram = $this->faker->regexify('[A-Z]{4}');
            $browser->visit($page->editUrl($team->id))
                ->type('trigram', $trigram)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($team->id))
                ->assertSeeIn('@trigram-error', 'The trigram must be 3 characters.')
                ->assertVisible('@form-errors');
        });
    }

    public function testShowTeam()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Team $team */
            $team = factory(Team::class)->create();
            $linkText = $team->team;

            $page = new TeamsPage();

            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) use ($linkText) {
                    $table->clickLink($linkText);
                })
                ->assertPathIs($page->showUrl($team->id))
                ->assertSeeIn('tbody tr td:nth-child(1)', $team->id)
                ->assertSeeIn('tbody tr td:nth-child(2)', $team->club->club)
                ->assertSeeIn('tbody tr td:nth-child(3)', $team->team)
                ->assertSeeIn('tbody tr td:nth-child(4)', $team->trigram);
        });
    }

    public function testDeleteTeam()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Team $team */
            $team = factory(Team::class)->create();

            $page = new TeamsPage();

            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('No');
                })
                ->assertDontSee('Team deleted!')
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Team deleted!');

            // Delete team in existing fixture as home team
            $team = factory(Team::class)->create();
            factory(Fixture::class)->create(['home_team_id' => $team->id]);
            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Cannot delete because they are existing fixtures for this team.');

            // Delete team in existing fixture as away team
            $team = factory(Team::class)->create();
            factory(Fixture::class)->create(['away_team_id' => $team->id]);
            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Cannot delete because they are existing fixtures for this team.');
        });
    }
}
