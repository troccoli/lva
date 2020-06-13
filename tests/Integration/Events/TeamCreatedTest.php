<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Team;
use App\Models\User;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class TeamCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testTeamSecretaryRoleIsCreated(): void
    {
        /** @var Team $team */
        $team = factory(Team::class)->create();

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::teamSecretaryName($team)]);
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
        $clubId = $team->getClub()->getId();

        /** @var User $teamSecretary */
        $teamSecretary = factory(User::class)->create();
        $teamSecretary->assignRole(RolesHelper::teamSecretaryName($team));

        $this->assertUserCan($teamSecretary, "view-teams-in-club-$clubId")
            ->assertUserCan($teamSecretary, "edit-team-$teamId");
        $this->assertUserCannot($teamSecretary, "delete-team-$teamId");

        /** @var User $clubSecretary */
        $clubSecretary = factory(User::class)->create();
        $clubSecretary->assignRole(RolesHelper::clubSecretaryName($team->getClub()));

        $this->assertUserCan($clubSecretary, "edit-team-$teamId");
        $this->assertUserCan($clubSecretary, "delete-team-$teamId");
    }
}
