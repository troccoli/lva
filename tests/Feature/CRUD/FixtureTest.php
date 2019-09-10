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

    public function testAccessForUnverifiedUsers(): void
    {
        $fixture = aFixture()->build();

        $this->actingAs(factory(User::class)->state('unverified')->create());

        $this->get('/fixtures')
            ->assertRedirect('/email/verify');
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        $fixture = aFixture()->build();

        $this->actingAs(factory(User::class)->create());

        $this->get('/fixtures')
            ->assertOk();
    }
}
