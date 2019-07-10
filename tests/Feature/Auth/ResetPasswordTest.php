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

    /** @var User */
    private $user;
    /** @var string */
    private $token;

    public function testResettingPassword(): void
    {
        // Missing token, email and password
        $this->post('/password/reset')
            ->assertSessionHasErrors(['token', 'email', 'password']);

        // Password mismatch
        $this->post('/password/reset', [
            'token'                 => $this->token,
            'email'                 => $this->user->email,
            'password'              => 'password123',
            'password_confirmation' => 'password',
        ])
            ->assertSessionHasErrors(['password']);

        // Wrong email address
        $this->post('/password/reset', [
            'token'                 => $this->token,
            'email'                 => 'tom@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasErrors(['email']);

        // Everything is fine
        $this->post('/password/reset', [
            'token'                 => $this->token,
            'email'                 => $this->user->email,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasNoErrors();
    }

    public function testResettingPasswordWithExpiredToken(): void
    {
        Carbon::setTestNow(Carbon::now()->addHours(2));
        $this->post('/password/reset', [
            'token'                 => $this->token,
            'email'                 => $this->user->email,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertSessionHasErrors(['email']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        DB::table('password_resets')
            ->insert([
                'email'      => $this->user->email,
                'token'      => app()->make(Hasher::class)->make($this->token = Str::random(32)),
                'created_at' => Carbon::now(),
            ]);
    }
}
