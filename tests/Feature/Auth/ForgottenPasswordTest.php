<?php

namespace Tests\Feature;

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
        $user = factory(User::class)->create();

        // Missing email
        $this->post('/password/email', [])
            ->assertSessionHasErrors(['email']);

        // Wrong format
        $this->post('/password/email', [
            'email' => 'a',
        ])->assertSessionHasErrors(['email']);

        // Non-existing user
        $this->post('/password/email', [
            'email' => 'tom@example.org',
        ])->assertSessionHasNoErrors();

        // Existing user
        $this->post('/password/email', [
            'email' => $user->email,
        ])->assertSessionHasNoErrors();
    }
}
