<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use LVA\User;
use Tests\Browser\Pages\DataManagementPage;
use Tests\DuskTestCase;

class DataManagementTest extends DuskTestCase
{
    public function testSeasonsTableButton()
    {
        $this->browse(function (Browser $browser) {
            /** @var User $user */
            $user = factory(User::class)->create();

            $page = new DataManagementPage();
            $browser->loginAs($user)
                // Using $page->url() instead of $page allows me to use the with() method
                // without calling the assert() method on the page, which would fail because
                // it won't be able to find the breadcrumb (inside the panels)
                ->visit($page->url())
                ->with('#crud-panel', function (Browser $panel) {
                    $panel->assertSeeLink('Seasons')
                        ->assertSeeLink('Divisions')
                        ->assertSeeLink('Venues')
                        ->assertSeeLink('Clubs')
                        ->assertSeeLink('Teams')
                        ->assertSeeLink('Roles')
                        ->assertSeeLink('Fixtures')
                        ->assertSeeLink('Available appointments');
                })
                ->with('#start-season-panel', function (Browser $panel) {
                    $panel->assertSeeLink('Load fixtures');
                })
                // Now visit the page so that its assert() method is run
                ->visit($page)
                ->clickLink('Season')
                ->assertRouteIs('seasons.index')
                ->back()
                ->clickLink('Divisions')
                ->assertRouteIs('divisions.index')
                ->back()
                ->clickLink('Venues')
                ->assertRouteIs('venues.index')
                ->back()
                ->clickLink('Clubs')
                ->assertRouteIs('clubs.index')
                ->back()
                ->clickLink('Teams')
                ->assertRouteIs('teams.index')
                ->back()
                ->clickLink('Roles')
                ->assertRouteIs('roles.index')
                ->back()
                ->clickLink('Fixtures')
                ->assertRouteIs('fixtures.index')
                ->back()
                ->clickLink('Available appointments')
                ->assertRouteIs('available-appointments.index')
                ->back()
                ->clickLink('Load fixtures')
                ->assertRouteIs('uploadFixtures');
        });
    }
}
