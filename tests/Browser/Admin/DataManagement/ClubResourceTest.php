<?php

namespace Tests\Browser\Admin\DataManagement;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use LVA\Models\Club;
use LVA\Models\Team;
use LVA\User;
use Tests\Browser\Pages\Resources\ClubsPage;
use Tests\DuskTestCase;

class ClubResourceTest extends DuskTestCase
{
    public function testRedirectIfNotAdmin()
    {
        $page = new ClubsPage();

        $this->browse(function (Browser $browser) use ($page) {
            $club = factory(Club::class)->create();

            $browser->visit($page->indexUrl())
                ->assertRouteIs('login');

            $browser->visit($page->createUrl())
                ->assertRouteIs('login');

            $browser->visit($page->showUrl($club->id))
                ->assertRouteIs('login');

            $browser->visit($page->editUrl($club->id))
                ->assertRouteIs('login');

        });
    }

    public function testListClubs()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Collection $clubs */
            $clubs = factory(Club::class)->times(20)->create();

            $page1 = $clubs->slice(0, 15);
            $page2 = $clubs->slice(15, 5);

            $page = new ClubsPage();
            $browser->visit($page)
                ->assertSeeIn($page->breadcrumb, 'Clubs')
                ->assertSeeLink('New club')
                ->with('tbody', function ($table) use ($page1) {
                    $child = 1;
                    foreach ($page1 as $club) {
                        $table->with("tr:nth-child($child)", function ($row) use ($club) {
                            $linkText = $club->club;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText);
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
                    foreach ($page2 as $club) {
                        $table->with("tr:nth-child($child)", function ($row) use ($club) {
                            $linkText = $club->club;
                            $row->assertSeeLink($linkText)
                                ->assertSeeIn('td:nth-child(1)', $linkText);
                        });
                        $child++;
                    }
                });
        });
    }

    public function testAddClub()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            $page = new ClubsPage();

            // Check we can add a division from the landing page
            $browser->visit($page)
                ->clickLink('New club')
                ->assertPathIs($page->createUrl());

            // All fields missing
            $browser->visit($page->createUrl())
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@club-error', 'The club field is required.')
                ->assertVisible('@form-errors');

            /** @var Club $club */
            $club = factory(Club::class)->make();
            // Brand new club
            $browser->visit($page->createUrl())
                ->type('club', $club->club)
                ->pressSubmit('Add')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Club added!');

            // Add the same club
            $browser->visit($page->createUrl())
                ->type('club', $club->club)
                ->pressSubmit('Add')
                ->assertPathIs($page->createUrl())
                ->assertSeeIn('@club-error', 'The club already exists.')
                ->assertVisible('@form-errors');
        });
    }

    public function testEditClub()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Club $club */
            $club = factory(Club::class)->create();

            $page = new ClubsPage();

            // Check we can edit a club from the landing page
            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) {
                    $table->clickLink('Update');
                })
                ->assertPathIs($page->editUrl($club->id));

            // Don't change anything
            $browser->visit($page->editUrl($club->id))
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Club updated!');

            /** @var Club $newClub */
            $newClub = factory(Club::class)->make();

            // Edit all details
            $browser->visit($page->editUrl($club->id))
                ->type('club', $newClub->club)
                ->pressSubmit('Update')
                ->assertPathIs($page->indexUrl())
                ->assertSeeIn('@success-notification', 'Club updated!');

            /** @var Club $newClub */
            $newClub = factory(Club::class)->create();

            // Use an already existing club
            $browser->visit($page->editUrl($club->id))
                ->type('club', $newClub->club)
                ->pressSubmit('Update')
                ->assertPathIs($page->editUrl($club->id))
                ->assertSeeIn('@club-error', 'The club already exists.')
                ->assertVisible('@form-errors');
        });
    }

    public function testShowClub()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Club $club */
            $club = factory(Club::class)->create();
            $linkText = $club->club;

            $page = new ClubsPage();

            $browser->visit($page)
                ->with($page->resourcesListTable, function ($table) use ($linkText) {
                    $table->clickLink($linkText);
                })
                ->assertPathIs($page->showUrl($club->id))
                ->assertSeeIn('tbody tr td:nth-child(1)', $club->id)
                ->assertSeeIn('tbody tr td:nth-child(2)', $club->club);
        });
    }

    public function testDeleteClub()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create());

            /** @var Club $club */
            $club = factory(Club::class)->create();

            $page = new ClubsPage();

            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('No');
                })
                ->assertDontSee('Club deleted!')
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Club deleted!');

            // Delete club with existing team
            $club = factory(Club::class)->create();
            factory(Team::class)->create(['club_id' => $club->id]);
            $browser->visit($page->indexUrl())
                ->press('Delete')
                ->whenAvailable('.popover.confirmation', function ($popover) {
                    $popover->clickLink('Yes');
                })
                ->assertSee('Cannot delete because they are existing teams in this club.');
        });
    }
}
