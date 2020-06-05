<?php

namespace Tests\Integration\Events;

use App\Models\Team;
use App\Models\User;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class TeamCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testTeamSecretaryRoleIsCreated(): void
    {
        $team = factory(Team::class)->create();

        $this->assertDatabaseHas('roles', ['name' => $team->getSecretaryRole()]);
    }

    public function testTeamPermissionsAreCreated(): void
    {
        $team = aTeam()->build();

        $this->assertDatabaseHas('permissions', ['name' => "edit-team-{$team->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-team-{$team->getId()}"]);
    }

    public function testAdminRolesHaveTheCorrectPermissions(): void
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();
        $teamId = $team->getId();

        /** @var User $teamSecretary */
        $teamSecretary = factory(User::class)->create();
        $teamSecretary->assignRole($team->getSecretaryRole());

        $this->assertUserCan($teamSecretary, "edit-team-$teamId");
        $this->assertUserCannot($teamSecretary, "delete-team-$teamId");

        /** @var User $clubSecretary */
        $clubSecretary = factory(User::class)->create();
        $clubSecretary->assignRole($team->getClub()->getSecretaryRole());

        $this->assertUserCan($clubSecretary, "edit-team-$teamId");
        $this->assertUserCan($clubSecretary, "delete-team-$teamId");
    }
}
