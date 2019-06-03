<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ForgottenPasswordTest extends DuskTestCase
{
    public function testRequestingResetForNonExistingUser(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/password/reset')
                ->type('email', 'tom@example.org')
                ->press('SEND PASSWORD RESET LINK')
                ->assertPathIs('/password/reset')
                ->assertSee('We have e-mailed your password reset link!');
        });
    }

    public function testRequestingRestForExistingUser(): void
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->create();
            $browser->visit('/password/reset')
                ->type('email', $user->email)
                ->press('SEND PASSWORD RESET LINK')
                ->assertPathIs('/password/reset')
                ->assertSee('We have e-mailed your password reset link!');
        });
    }

    public function testRequestingRestForUnverifiedUser(): void
    {
        $this->browse(function (Browser $browser) {
            $user = factory(User::class)->state('unverified')->create();
            $browser->visit('/password/reset')
                ->type('email', $user->email)
                ->press('SEND PASSWORD RESET LINK')
                ->assertPathIs('/password/reset')
                ->assertSee('We have e-mailed your password reset link!');
        });
    }
}
