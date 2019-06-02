<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class ResetPasswordTest extends DuskTestCase
{
    public function testRestPasswordJourney(): void
    {
        $user = factory(User::class)->create();
        $this->browse(function (Browser $browser) use ($user): void {
            $browser->visit('/password/reset')
                ->type('email', $user->email)
                ->press('SEND PASSWORD RESET LINK');

            $this->fixStoredToken($user->email, $token = Str::random(32));
            $browser->visit('/password/reset/' . $token . '?email=' . urlencode($user->email))
                ->assertInputValue('email', $user->email)
                ->type('password', 'password123')
                ->type('password_confirmation', 'password123')
                ->press('RESET PASSWORD')
                ->assertPathIs('/dashboard')
                ->logout();

            $user->refresh();
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password123')
                ->press('LOGIN')
                ->assertAuthenticatedAs($user);
        });
    }

    private function fixStoredToken(string $email, string $token): void
    {
        $hasher = app()->make(Hasher::class);
        DB::table('password_resets')
            ->where('email', $email)
            ->update([
                'token' => $hasher->make($token),
            ]);
    }
}
