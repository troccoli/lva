<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

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
        $user = factory(User::class)->create(['email' => 'john@example.com']);

        // Missing required fields
        $this->post('/login', [])
            ->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();

        // Wrong password
        $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'secret'
        ])->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // Invalid email address
        $this->post('/login', [
            'email' => 'a',
            'password' => 'password'
        ])->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // Non-existing email
        $this->post('/login', [
            'email' => 'tom@example.org',
            'password' => 'password'
        ])->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // OK
        $this->post('/login', [
            'email'    => 'john@example.com',
            'password' => 'password',
        ])->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }
}
