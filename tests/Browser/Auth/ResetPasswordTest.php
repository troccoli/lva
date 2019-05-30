<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ResetPasswordTest extends DuskTestCase
{
    public function testRequestingResetForNonExistingUser(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/password/reset')
                ->type('email', 'tom@example.org')
                ->press('Send Password Reset Link')
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
                ->press('Send Password Reset Link')
                ->assertPathIs('/password/reset')
                ->assertSee('We have e-mailed your password reset link!');
        });
    }
}
