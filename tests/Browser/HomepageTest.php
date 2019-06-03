<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomepageTest extends DuskTestCase
{
    public function testForGuests(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('London Volleyball Association');
        });
    }

    public function testForAuthenticatedUsers(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(factory(User::class)->create())
                ->visit('/')
                ->assertSee('London Volleyball Association');
        });
    }

    public function testForUnverifiedUsers(): void
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->state('unverified')->create();
            $browser->loginAs($user)
                ->visit('/')
                ->assertSee('London Volleyball Association');
        });
    }
}
