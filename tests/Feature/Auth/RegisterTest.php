<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $this->get('/register')
            ->assertOk();
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        $this->actingAs(factory(User::class)->create())
            ->get('/register')
            ->assertRedirect('/dashboard');
    }

    public function testRegistering(): void
    {
        $existingUser = factory(User::class)->create();
        $user = factory(User::class)->make();

        // Missing name, email and password
        $this->post('/register', [])
            ->assertSessionHasErrors(['name', 'email', 'password']);

        // Missing confirmation password
        $this->post('/register', [
            'password' => 'password',
        ])->assertSessionHasErrors(['password']);

        // Password mismatch
        $this->post('/register', [
            'password' => 'password',
            'password_confirmation' => 'secret'
        ])->assertSessionHasErrors(['password']);

        // Wrong email
        $this->post('/register', [
            'email' => 'a',
        ])->assertSessionHasErrors(['email']);

        // Existing email
        $this->post('/register', [
            'email' => $existingUser->email,
        ])->assertSessionHasErrors(['email']);

        // OK test
        $this->post('/register', [
            'name' => $user->name,
            'email'    => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}
