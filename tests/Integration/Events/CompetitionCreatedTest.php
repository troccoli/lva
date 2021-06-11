<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class CompetitionCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testCompetitionAdminRoleIsCreated(): void
    {
        /** @var Competition $competition */
        $competition = Competition::factory()->create();

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::competitionAdmin($competition)]);
    }

    public function testCompetitionPermissionsAreCreated(): void
    {
        /** @var Competition $competition */
        $competition = Competition::factory()->create();
        $competitionId = $competition->getId();

        $this->assertDatabaseHas('permissions', ['name' => "view-competition-$competitionId"]);
        $this->assertDatabaseHas('permissions', ['name' => "edit-competition-$competitionId"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-competition-$competitionId"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-division-in-competition-$competitionId"]);
    }

    public function testAdminRolesHaveTheCorrectPermissions(): void
    {
        /** @var Competition $competition */
        $competition = Competition::factory()->create();
        $competitionId = $competition->getId();
        $seasonId = $competition->getSeason()->getId();

        $competitionAdmin = $this->userWithRole(RolesHelper::competitionAdmin($competition));

        $this->assertUserCan($competitionAdmin, "view-competition-$competitionId")
             ->assertUserCan($competitionAdmin, "edit-competition-$competitionId")
             ->assertUserCan($competitionAdmin, "add-division-in-competition-$competitionId")
             ->assertUserCan($competitionAdmin, "view-season-$seasonId");
        $this->assertUserCannot($competitionAdmin, "delete-competition-$competitionId");

        $seasonAdmin = $this->userWithRole(RolesHelper::seasonAdmin($competition->getSeason()));

        $this->assertUserCan($seasonAdmin, "view-competition-$competitionId")
             ->assertUserCan($seasonAdmin, "edit-competition-$competitionId")
             ->assertUserCan($seasonAdmin, "add-division-in-competition-$competitionId")
             ->assertUserCan($seasonAdmin, "delete-competition-$competitionId");
    }
}
