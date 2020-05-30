<?php

namespace Tests\Integration\Events;

use App\Models\Club;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClubCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function testClubSecretaryRoleIsCreated(): void
    {
        $club = factory(Club::class)->create();

        $this->assertDatabaseHas('roles', ['name' => $club->getSecretaryRole()]);
    }

    public function testClubPermissionsAreCreated(): void
    {
        $club = aClub()->build();

        $this->assertDatabaseHas('permissions', ['name' => "edit-club-{$club->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-club-{$club->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-teams-in-club-{$club->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-teams-in-club-{$club->getId()}"]);
    }
}
