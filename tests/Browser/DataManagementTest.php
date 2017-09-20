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
                ->visit($page)
                ->assertSeeLink('Season')
                ->clickLink('Season')
                ->assertRouteIs('seasons.index')
                ->visit($page)
                ->assertSeeLink('Divisions')
                ->clickLink('Divisions')
                ->assertRouteIs('divisions.index')
                ->visit($page)
                ->assertSeeLink('Venues')
                ->clickLink('Venues')
                ->assertRouteIs('venues.index')
                ->visit($page)
                ->assertSeeLink('Clubs')
                ->clickLink('Clubs')
                ->assertRouteIs('clubs.index')
                ->visit($page)
                ->assertSeeLink('Teams')
                ->clickLink('Teams')
                ->assertRouteIs('teams.index')
                ->visit($page)
                ->assertSeeLink('Roles')
                ->clickLink('Roles')
                ->assertRouteIs('roles.index')
                ->visit($page)
                ->assertSeeLink('Fixtures')
                ->clickLink('Fixtures')
                ->assertRouteIs('fixtures.index')
                ->visit($page)
                ->assertSeeLink('Available appointments')
                ->clickLink('Available appointments')
                ->assertRouteIs('available-appointments.index');
        });
    }
}
