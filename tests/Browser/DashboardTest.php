<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class DashboardTest extends DuskTestCase
{
    public function testForGuests(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/dashboard')
                ->assertPathIs('/login');
        });
    }

    public function testForAuthenticatedUsers(): void
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->assertSee('London Volleyball Association')
                ->assertSee('Dashboard')
                ->assertSee($user->name);
        });
    }
}
