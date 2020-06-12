<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Season;
use App\Models\User;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class SeasonCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testSeasonAdminRoleIsCreated(): void
    {
        $season = factory(Season::class)->create();

        $this->assertDatabaseHas('roles', ['name' => "Season {$season->getId()} Administrator"]);
    }

    public function testSeasonsPermissionsAreCreated(): void
    {
        $season = factory(Season::class)->create();

        $this->assertDatabaseHas('permissions', ['name' => "edit-season-{$season->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-season-{$season->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-competition-in-season-{$season->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-competitions-in-season-{$season->getId()}"]);
    }

    public function testSeasonAdminRoleHasTheCorrectPermissions(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create();
        $seasonId = $season->getId();

        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(RolesHelper::seasonAdminName($season));

        $this->assertUserCan($user, "edit-season-$seasonId")
            ->assertUserCan($user, "add-competition-in-season-$seasonId")
            ->assertUserCan($user, "view-competitions-in-season-$seasonId");

        $this->assertUserCannot($user, "delete-season-$seasonId");
    }
}
