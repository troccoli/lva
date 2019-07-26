<?php

namespace Tests\Browser\CRUD;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Dusk\Browser;
use App\Models\Club;
use App\Models\User;
use Tests\DuskTestCase;

class ClubTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testListClubs(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            $browser->visit('/clubs')
                ->assertSeeIn('@list', 'There are no clubs yet.');

            /** @var Collection $clubs */
            $clubs = factory(Club::class)->times(25)->create()->sortBy('name');

            $page1 = $clubs->slice(0, 15);
            $page2 = $clubs->slice(15, 15);

            $browser->visit('/clubs')
                ->assertSeeLink('New club')
                ->with('@list', function (Browser $table) use ($page1): void {
                    $child = 1;
                    foreach ($page1 as $index => $club) {
                        /** @var Club $club */
                        $table->with("tr:nth-child($child)", function (Browser $row) use ($club): void {
                            $row->assertSeeIn('td:nth-child(1)', $club->getName());
                        });
                        $child++;
                    }
                })
                ->with('div.pagination', function (Browser $nav): void {
                    $nav->clickLink(2);
                })
                ->assertPathIs('/clubs')
                ->assertQueryStringHas('page', 2)
                ->with('@list', function (Browser $table) use ($page2): void {
                    $child = 1;
                    foreach ($page2 as $club) {
                        /** @var Club $club */
                        $table->with("tr:nth-child($child)", function (Browser $row) use ($club): void {
                            $row->assertSeeIn('td:nth-child(1)', $club->getName());
                        });
                        $child++;
                    }
                });
        });
    }

    /**
     * @throws \Throwable
     */
    public function testAddClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            // Check we can add a club from the landing page
            $browser->visit('/clubs')
                ->clickLink('New club')
                ->assertPathIs('/clubs/create');

            $browser->visit("/clubs/create")
                ->assertSee('Add a new club');

            // Check the form
            $browser->visit('/clubs/create')
                ->assertInputValue('name', '')
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'Add club');

            // All fields missing
            $browser->visit('/clubs/create')
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('Add club')
                ->assertPathIs('/clubs/create')
                ->assertSeeIn('@name-error', 'The name is required.');

            /** @var Club $club */
            $club = aClub()->buildWithoutSaving();
            // Brand new club
            $browser->visit('/clubs/create')
                ->type('name', $club->getName())
                ->press('Add club')
                ->assertPathIs('/clubs')
                ->assertSee('Club added!');

            // Add the same club
            $browser->visit('/clubs/create')
                ->type('name', $club->getName())
                ->press('Add club')
                ->assertPathIs('/clubs/create')
                ->assertSeeIn('@name-error', 'The club already exists.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testEditClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Club $club */
            $club = aClub()->build();

            // Check we can edit a club from the landing page
            $browser->visit('/clubs')
                ->with('@list', function (Browser $table): void {
                    $table->clickLink('Update');
                })
                ->assertPathIs('/clubs/' . $club->getId() . '/edit')
                ->assertSee("Edit the {$club->getName()} club");

            // Check the form
            $browser->visit('/clubs/' . $club->getId() . '/edit')
                ->assertInputValue('name', $club->getName())
                ->assertVisible('@submit-button')
                ->assertSeeIn('@submit-button', 'Save changes');

            // Don't change anything
            $browser->visit('/clubs/' . $club->getId() . '/edit')
                ->press('Save changes')
                ->assertPathIs('/clubs')
                ->assertSee('Club updated!');

            /** @var Club $newClub */
            $newClub = aClub()->buildWithoutSaving();

            // Remove required fields
            $browser->visit('/clubs/' . $club->getId() . '/edit')
                ->type('name', ' ')// This is to get around the HTML5 validation on the browser
                ->press('Save changes')
                ->assertPathIs('/clubs/' . $club->getId() . '/edit')
                ->assertSeeIn('@name-error', 'The name is required.');

            // Edit all details
            $browser->visit('/clubs/' . $club->getId() . '/edit')
                ->type('name', $newClub->getName())
                ->press('Save changes')
                ->assertPathIs('/clubs')
                ->assertSee('Club updated!');

            $newClub = aClub()->build();

            // Use an already existing club
            $browser->visit('/clubs/' . $club->getId() . '/edit')
                ->type('name', $newClub->getName())
                ->press('Save changes')
                ->assertPathIs('/clubs/' . $club->getId() . '/edit')
                ->assertSeeIn('@name-error', 'The club already exists.');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testDeleteClub(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Club $club */
            $club = aClub()->build();

            $browser->visit('/clubs')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Cancel')
                        ->pause(1000);
                })
                ->assertDontSee('Club deleted!')
                ->assertSee($club->getName());
            $browser->visit('/clubs')
                ->press('Delete')
                ->whenAvailable('.bootbox-confirm', function (Browser $modal): void {
                    $modal->assertSee('Are you sure?')
                        ->press('Confirm')
                        ->pause(1000);
                })
                ->assertSee('Club deleted!')
                ->assertDontSee($club->getName());
        });
    }

    /**
     * @throws \Throwable
     */
    public function testViewTeams(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(factory(User::class)->create());

            /** @var Club $club */
            $club = aClub()->build();

            aTeam()->inClub($club)->build(2);

            $browser->visit('/clubs')
                ->with('@list', function (Browser $table): void {
                    $table->clickLink('View');
                })
                ->assertPathIs('/clubs/' . $club->getId() . '/teams');
        });
    }
}
