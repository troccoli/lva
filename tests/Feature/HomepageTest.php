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
        $this->actingAs(factory(User::class)->create())
            ->get('/')
            ->assertOk();
    }

    public function testAccessForUnverifiedUsers(): void
    {
        $this->actingAs(factory(User::class)->state('unverified')->create())
            ->get('/')
            ->assertOk();
    }

    public function testBreadcrumbs(): void
    {
        $this->get('/')
            ->assertSeeTextInOrder(['Home']);
    }
}
