<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\User;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class ClubCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testClubSecretaryRoleIsCreated(): void
    {
        $club = factory(Club::class)->create();

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::clubSecretaryName($club)]);
    }

    public function testClubPermissionsAreCreated(): void
    {
        $club = aClub()->build();

        $this->assertDatabaseHas('permissions', ['name' => "edit-club-{$club->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-club-{$club->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-teams-in-club-{$club->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-teams-in-club-{$club->getId()}"]);
    }

    public function testClubSecretaryRoleHasTheCorrectPermissions(): void
    {
        /** @var Club $club */
        $club = factory(Club::class)->create();
        $clubId = $club->getId();

        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(RolesHelper::clubSecretaryName($club));

        $this->assertUserCan($user, "edit-club-$clubId")
            ->assertUserCan($user, "add-teams-in-club-$clubId")
            ->assertUserCan($user, "view-teams-in-club-$clubId");

        $this->assertUserCannot($user, "delete-club-$clubId");
    }
}
