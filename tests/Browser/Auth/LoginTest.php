<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testLoggingInForNonExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/login')
                    ->type('email', 'tom@example.org')
                    ->type('password', 'password')
                    ->press('LOGIN')
                    ->assertPathIs('/login')
                    ->assertSee('These credentials do not match our records.')
                    ->assertGuest();
        });
    }

    /**
     * @throws \Throwable
     */
    public function testLoggingInForExistingUser(): void
    {
        $this->browse(
            function (Browser $browser): void {
                $user = User::factory()->create(['email' => 'john@example.com']);
                $browser->visit('/login')
                        ->type('email', 'john@example.com')
                        ->type('password', 'secret')
                        ->press('LOGIN')
                        ->assertPathIs('/login')
                        ->assertSee('These credentials do not match our records.')
                        ->assertGuest();
                $browser->visit('/login')
                        ->type('email', 'john@example.com')
                        ->type('password', 'password')
                        ->press('LOGIN')
                        ->assertPathIs('/dashboard')
                        ->assertAuthenticatedAs($user);
            }
        );
    }

    /**
     * @throws \Throwable
     */
    public function testLoggingInForUnverifiedUsers(): void
    {
        $this->browse(
            function (Browser $browser): void {
                User::factory()->unverified()->create(['email' => 'john@example.com']);
                $browser->visit('/login')
                        ->type('email', 'john@example.com')
                        ->type('password', 'password')
                        ->press('LOGIN')
                        ->assertSee('Verify your email address!')
                        ->assertPathIs('/email/verify')
                        ->assertAuthenticated();
            }
        );
    }
}
