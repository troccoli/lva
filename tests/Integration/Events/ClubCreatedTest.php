<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Club;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class ClubCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testClubSecretaryRoleIsCreated(): void
    {
        $club = factory(Club::class)->create();

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::clubSecretary($club)]);
    }

    public function testClubPermissionsAreCreated(): void
    {
        $clubId = aClub()->build()->getId();

        $this->assertDatabaseHas('permissions', ['name' => "view-club-$clubId"]);
        $this->assertDatabaseHas('permissions', ['name' => "edit-club-$clubId"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-club-$clubId"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-team-in-club-$clubId"]);
    }

    public function testClubSecretaryRoleHasTheCorrectPermissions(): void
    {
        $club = aClub()->build();
        $clubId = $club->getId();

        $user = $this->userWithRole(RolesHelper::clubSecretary($club));

        $this->assertUserCan($user, "view-club-$clubId")
            ->assertUserCan($user, "edit-club-$clubId")
            ->assertUserCan($user, "add-team-in-club-$clubId")
            ->assertUserCannot($user, "delete-club-$clubId");

        $this->assertUserCan($this->siteAdmin, "add-club");
        $this->assertUserCan($this->siteAdmin, "delete-club-$club");
    }
}
