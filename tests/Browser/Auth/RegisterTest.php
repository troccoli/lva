<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class RegisterTest extends DuskTestCase
{
    public function testRegisteringForNonExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $user = factory(User::class)->make();
            $browser->visit('/register')
                ->type('name', $user->name)
                ->type('email', $user->email)
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('REGISTER')
                ->assertPathIs('/dashboard');
        });
    }

    public function testRegisteringForExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $user = factory(User::class)->create();
            $browser->visit('/register')
                ->type('name', 'Tom')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('REGISTER')
                ->assertPathIs('/register')
                ->assertSee('The email has already been taken.');
        });
    }
}
