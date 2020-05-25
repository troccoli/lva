<?php

namespace Tests\Integration\Events;

use App\Models\Club;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateClubSecretaryRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testClubSecretaryRoleIsCreatedWhenClubIsCreated(): void
    {
        $club = factory(Club::class)->create();

        $this->assertDatabaseHas('roles', ['name' => $club->getSecretaryRole()]);
    }
}
