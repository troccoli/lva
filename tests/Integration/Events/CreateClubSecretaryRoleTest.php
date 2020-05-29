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

    public function testClubPermissionsAreCreatedWhenClubIsCreated(): void
    {
        $club = aClub()->build();

        $this->assertDatabaseHas('permissions', ['name' => "edit-club-{$club->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-club-{$club->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-teams-in-club-{$club->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-teams-in-club-{$club->getId()}"]);
    }
}
