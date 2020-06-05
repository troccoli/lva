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
        $this->actingAs(factory(User::class)->create())
            ->get('/dashboard')
            ->assertOk();
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $this->actingAs(factory(User::class)->state('unverified')->create())
            ->get('/dashboard')
            ->assertRedirect('/email/verify');
    }
}
