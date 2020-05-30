<?php

namespace Tests\Feature\CRUD;

use App\Models\User;
use Tests\TestCase;

class FixtureTest extends TestCase
{
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

        $this->actingAs(factory(User::class)->create()->assignRole('Site Administrator'));

        $this->get('/fixtures')
            ->assertOk();
    }
}
