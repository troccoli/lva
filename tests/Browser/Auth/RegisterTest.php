<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class RegisterTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testRegisteringForNonExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/register')
                ->type('name', 'Tom')
                ->type('email', 'tom@example.org')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('REGISTER')
                ->assertPathIs('/email/verify')
                ->assertAuthenticated();
        });
    }

    /**
     * @throws \Throwable
     */
    public function testRegisteringForExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            factory(User::class)->create(['email' => 'john@example.com']);
            $browser->visit('/register')
                ->type('name', 'Tom')
                ->type('email', 'john@example.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('REGISTER')
                ->assertPathIs('/register')
                ->assertSee('The email has already been taken.')
                ->assertGuest();
        });
    }
}
