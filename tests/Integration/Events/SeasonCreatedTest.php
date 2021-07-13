<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Season;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class SeasonCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testSeasonAdminRoleIsCreated(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create();

        $this->assertDatabaseHas('roles', ['name' => "Season {$season->getId()} Administrator"]);
    }

    public function testSeasonsPermissionsAreCreated(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create();
        $seasonId = $season->getId();

        $this->assertDatabaseHas('permissions', ['name' => "view-season-$seasonId"]);
        $this->assertDatabaseHas('permissions', ['name' => "edit-season-$seasonId"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-season-$seasonId"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-competition-in-season-$seasonId"]);
    }

    public function testSeasonAdminRoleHasTheCorrectPermissions(): void
    {
        /** @var Season $season */
        $season = Season::factory()->create();
        $seasonId = $season->getId();

        $user = $this->userWithRole(RolesHelper::seasonAdmin($season));

        $this->assertUserCan($user, "view-season-$seasonId")
             ->assertUserCan($user, "edit-season-$seasonId")
             ->assertUserCan($user, "add-competition-in-season-$seasonId");
        $this->assertUserCannot($user, 'add-season')
             ->assertUserCannot($user, "delete-season-$seasonId");

        $this->assertUserCan($this->siteAdmin, 'add-season');
        $this->assertUserCan($this->siteAdmin, "delete-season-$seasonId");
    }
}
