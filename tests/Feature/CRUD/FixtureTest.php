<?php

namespace Tests\Feature\CRUD;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixtureTest extends TestCase
{
    use RefreshDatabase;

    public function testAccessForGuests(): void
    {
        $fixture = aFixture()->build();

        $this->get('/fixtures')
            ->assertRedirect('/login');
    }

    public function testAccessForUserWithoutThePermission(): void
    {
        $fixture = aFixture()->build();

        $this->actingAs(factory(User::class)->create());

        $this->get('/fixtures')
            ->assertForbidden();
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $fixture = aFixture()->build();

        $this->actingAs(factory(User::class)->state('unverified')->create());

        $this->get('/fixtures')
            ->assertRedirect('/email/verify');
    }

    public function testAccessForSuperAdmin(): void
    {
        $fixture = aFixture()->build();

        $this->actingAs(factory(User::class)->create()->assignRole('Site Admin'));

        $this->get('/fixtures')
            ->assertOk();
    }
}
