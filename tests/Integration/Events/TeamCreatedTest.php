<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Team;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class TeamCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testTeamSecretaryRoleIsCreated(): void
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::teamSecretary($team)]);
    }

    public function testTeamPermissionsAreCreated(): void
    {
        $teamId = aTeam()->build()->getId();

        $this->assertDatabaseHas('permissions', ['name' => "view-team-$teamId"]);
        $this->assertDatabaseHas('permissions', ['name' => "edit-team-$teamId"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-team-$teamId"]);
    }

    public function testAdminRolesHaveTheCorrectPermissions(): void
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();
        $teamId = $team->getId();
        $clubId = $team->getClub()->getId();

        $teamSecretary = $this->userWithRole(RolesHelper::teamSecretary($team));

        $this->assertUserCan($teamSecretary, "view-team-$teamId")
            ->assertUserCan($teamSecretary, "edit-team-$teamId")
            ->assertUserCan($teamSecretary, "view-club-$clubId")
            ->assertUserCannot($teamSecretary, "delete-team-$teamId");

        $clubSecretary = $this->userWithRole(RolesHelper::clubSecretary($team->getClub()));

        $this->assertUserCan($clubSecretary, "view-team-$teamId")
            ->assertUserCan($clubSecretary, "edit-team-$teamId")
            ->assertUserCan($clubSecretary, "delete-team-$teamId");
    }
}
