<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForgottenPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testAccessForGuests(): void
    {
        $this->get('/password/reset')
            ->assertOk();
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        $this->actingAs(factory(User::class)->create())
            ->get('/password/reset')
            ->assertRedirect('/dashboard');
    }

    public function testRequestingResetPassword(): void
    {
        $user = factory(User::class)->create(['email' => 'john@example.com']);

        // Missing email
        $this->post('/password/email', [])
            ->assertSessionHasErrors(['email']);

        // Wrong format
        $this->post('/password/email', [
            'email' => 'a',
        ])->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('password_resets', ['email' => 'a']);

        // Non-existing user
        $this->post('/password/email', [
            'email' => 'tom@example.org',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('password_resets', ['email' => 'tom@example.org']);

        // Existing user
        $this->post('/password/email', [
            'email' => 'john@example.com',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('password_resets', ['email' => 'john@example.com']);
    }
}
