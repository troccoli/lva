<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $this->get('/login')
            ->assertOk();
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        $this->actingAs(factory(User::class)->create())
            ->get('/login')
            ->assertRedirect('/dashboard');
    }

    public function testLoggingIn(): void
    {
        $user = factory(User::class)->create();

        // Missing required fields
        $this->post('/login', [])
            ->assertSessionHasErrors(['email', 'password']);

        // Wrong password
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret'
        ])->assertSessionHasErrors(['email']);

        // Invalid email address
        $this->post('/login', [
            'email' => 'a',
            'password' => 'secret'
        ])->assertSessionHasErrors(['email']);

        // Non-existing email
        $this->post('/login', [
            'email' => $user->email . '.com',
            'password' => 'password'
        ])->assertSessionHasErrors(['email']);

        // OK
        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertSessionHasNoErrors();
    }
}
