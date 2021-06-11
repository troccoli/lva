<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ResetPasswordTest extends DuskTestCase
{
    /**
     * @throws \Throwable
     */
    public function testRestPasswordJourney(): void
    {
        $this->browse(function (Browser $browser): void {
            $user = User::factory()->create(['email' => 'john@example.com']);
            $browser->visit('/password/reset')
                    ->type('email', 'john@example.com')
                    ->press('SEND PASSWORD RESET LINK');

            $this->fixStoredToken('john@example.com', 'THISiSaFan5st1cT0Ken#11@');
            $browser->visit('/password/reset/'.urlencode('THISiSaFan5st1cT0Ken#11@'))
                    ->type('email', 'john@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->press('RESET PASSWORD')
                    ->assertPathIs('/dashboard')
                    ->assertAuthenticatedAs($user);
        });
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function fixStoredToken(string $email, string $token): void
    {
        $hasher = app()->make(Hasher::class);
        DB::table('password_resets')
          ->where('email', $email)
          ->update(
              [
                  'token' => $hasher->make($token),
              ]
          );
    }
}
