<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class ForgottenPasswordTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $this->get('/password/reset')
             ->assertOk();
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        $this->actingAs(User::factory()->create())
             ->get('/password/reset')
             ->assertRedirect('/dashboard');
    }

    public function testRequestingResetPassword(): void
    {
        $user = User::factory()->create();

        // Missing email
        $this->post('/password/email', [])
             ->assertSessionHasErrors(['email']);

        // Wrong format
        $this->post(
            '/password/email',
            [
                'email' => 'a',
            ]
        )->assertSessionHasErrors(['email']);

        // Non-existing user
        $this->post(
            '/password/email',
            [
                'email' => 'tom@example.org',
            ]
        )->assertSessionHasNoErrors();

        // Existing user
        $this->post(
            '/password/email',
            [
                'email' => $user->email,
            ]
        )->assertSessionHasNoErrors();
    }
}
