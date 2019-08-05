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
    public function testRequestingRestForExistingUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $user = factory(User::class)->create();
            $browser->visit('/password/reset')
                ->type('email', $user->email)
                ->press('SEND PASSWORD RESET LINK')
                ->assertPathIs('/password/reset')
                ->assertSee('We have e-mailed your password reset link!');
        });
    }

    /**
     * @throws \Throwable
     */
    public function testRequestingRestForUnverifiedUser(): void
    {
        $this->browse(function (Browser $browser): void {
            $user = factory(User::class)->state('unverified')->create();
            $browser->visit('/password/reset')
                ->type('email', $user->email)
                ->press('SEND PASSWORD RESET LINK')
                ->assertPathIs('/password/reset')
                ->assertSee('We have e-mailed your password reset link!');
        });
    }
}
