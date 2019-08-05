<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        factory(User::class)->create(['email' => 'john@example.com']);
        DB::table('password_resets')
            ->insert([
                'email'      => 'john@example.com',
                'token'      => app()->make(Hasher::class)->make('THISiSaFan5st1cT0Ken#11@'),
                'created_at' => Carbon::now(),
            ]);
    }

    public function testResettingPassword(): void
    {
        // Missing token, email and password
        $this->post('/password/reset')
            ->assertSessionHasErrors(['token', 'email', 'password']);

        // Password mismatch
        $this->post('/password/reset', [
            'token'                 => 'THISiSaFan5st1cT0Ken#11@',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password',
        ])
            ->assertSessionHasErrors(['password']);

        // Wrong email address
        $this->post('/password/reset', [
            'token'                 => 'THISiSaFan5st1cT0Ken#11@',
            'email'                 => 'tom@example.org',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasErrors(['email']);

        // Everything is fine
        $this->post('/password/reset', [
            'token'                 => 'THISiSaFan5st1cT0Ken#11@',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasNoErrors();
    }

    public function testResettingPasswordWithExpiredToken(): void
    {
        Carbon::setTestNow(Carbon::now()->addHours(2));
        $this->post('/password/reset', [
            'token'                 => 'THISiSaFan5st1cT0Ken#11@',
            'email'                 => 'john@emxaple.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasErrors(['email']);
    }
}
