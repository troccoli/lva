<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

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
        factory(User::class)->create(['email' => 'john@example.com']);
        factory(User::class)->make(['email' => 'tom@example.org']);

        // Missing name, email and password
        $this->post('/register', [])
            ->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertDatabaseMissing('users', ['email' => 'tom@example.org']);

        // Missing confirmation password
        $this->post('/register', [
            'password' => 'password',
        ])->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'tom@example.org']);

        // Password mismatch
        $this->post('/register', [
            'password' => 'password',
            'password_confirmation' => 'secret'
        ])->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'tom@example.org']);

        // Wrong email
        $this->post('/register', [
            'email' => 'a',
        ])->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users', ['email' => 'tom@example.org']);

        // Existing email
        $this->post('/register', [
            'email' => 'john@example.com',
        ])->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users', ['email' => 'tom@example.org']);

        // OK test
        $this->post('/register', [
            'name' => 'Tom',
            'email'    => 'tom@example.org',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['name' => 'Tom', 'email' => 'tom@example.org']);
    }
}
