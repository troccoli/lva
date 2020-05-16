<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ForgottenPasswordTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testRequestingResetForNonExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/password/reset')
                ->type('email', 'tom@example.org')
                ->press('SEND PASSWORD RESET LINK')
                ->assertPathIs('/password/reset')
                ->assertSee('We have e-mailed your password reset link!');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testRequestingResetForExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            factory(User::class)->create(['email' => 'john@example.com']);
            $browser->visit('/password/reset')
                ->type('email', 'john@example.com')
                ->press('SEND PASSWORD RESET LINK')
                ->assertPathIs('/password/reset')
                ->assertSee('We have e-mailed your password reset link!');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testRequestingResetForUnverifiedUser(): void
    {
        $this->browse(function (Browser $browser): void {
            factory(User::class)->state('unverified')->create(['email' => 'john@example.com']);
            $browser->visit('/password/reset')
                ->type('email', 'john@exampe.com')
                ->press('SEND PASSWORD RESET LINK')
                ->assertPathIs('/password/reset')
                ->assertSee('We have e-mailed your password reset link!');
        });
    }
}
