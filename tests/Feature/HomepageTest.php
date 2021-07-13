<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    public function testAccessForGuests(): void
    {
        $this->get('/')
             ->assertOk();
    }

    public function testAccessForAuthenticatedUsers(): void
    {
        $this->actingAs(User::factory()->create())
             ->get('/')
             ->assertOk();
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
             ->get('/')
             ->assertOk();
    }

    public function testBreadcrumbs(): void
    {
        $this->get('/')
             ->assertSeeTextInOrder(['Home']);
    }
}
