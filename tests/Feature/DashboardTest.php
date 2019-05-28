<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

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

    public function testBreadcrumbs(): void
    {
        $this->actingAs(factory(User::class)->create())
            ->get('/dashboard')
            ->assertSeeTextInOrder(['Home', 'Dashboard']);
    }
}
