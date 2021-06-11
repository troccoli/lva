<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomepageTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testForGuests(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/')
                    ->assertSee('London Volleyball Association');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testForAuthenticatedUsers(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->loginAs(User::factory()->create())
                    ->visit('/')
                    ->assertSee('London Volleyball Association');
        });
    }

    public function testForUnverifiedUsers(): void
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->unverified()->create();
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('London Volleyball Association');
        });
    }
}
