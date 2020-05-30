<?php

namespace Tests\Integration\Events;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function testTeamSecretaryRoleIsCreatedWhenTeamIsCreated(): void
    {
        $team = factory(Team::class)->create();

        $this->assertDatabaseHas('roles', ['name' => $team->getSecretaryRole()]);
    }

    public function testTeamPermissionsAreCreatedWhenTeamIsCreated(): void
    {
        $team = aTeam()->build();

        $this->assertDatabaseHas('permissions', ['name' => "edit-team-{$team->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-team-{$team->getId()}"]);
    }
}
