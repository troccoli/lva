<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $this->get('/dashboard')
             ->assertRedirect('/login');
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        $this->actingAs(User::factory()->create())
             ->get('/dashboard')
             ->assertOk();
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
             ->get('/dashboard')
             ->assertRedirect('/email/verify');
    }
}
