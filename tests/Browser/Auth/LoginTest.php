<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LoginTest extends DuskTestCase
{
    public function testLoggingInForNonExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/login')
                ->type('email', 'tom@exampl.com')
                ->type('password', 'password')
                ->press('LOGIN')
                ->assertPathIs('/login')
                ->assertSee('These credentials do not match our records.');
        });
    }

    public function testLoggingInForExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $user = factory(User::class)->create();
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('LOGIN')
                ->assertPathIs('/dashboard')
                ->assertAuthenticatedAs($user);
        });
    }
}
