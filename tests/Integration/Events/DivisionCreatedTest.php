<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Division;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class DivisionCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testDivisionAdminRoleIsCreated(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::divisionAdmin($division)]);
    }

    public function testDivisionPermissionsAreCreated(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $divisionId = $division->getId();

        $this->assertDatabaseHas('permissions', ['name' => "view-division-$divisionId"]);
        $this->assertDatabaseHas('permissions', ['name' => "edit-division-$divisionId"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-division-$divisionId"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-fixtures-in-division-$divisionId"]);
        $this->assertDatabaseHas('permissions', ['name' => "edit-fixtures-in-division-$divisionId"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-fixtures-in-division-$divisionId"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-fixtures-in-division-$divisionId"]);
    }

    public function testAdminRolesHaveTheCorrectPermissions(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $divisionId = $division->getId();
        $competitionId = $division->getCompetition()->getId();
        $seasonId = $division->getCompetition()->getSeason()->getId();

        $divisionAdmin = $this->userWithRole(RolesHelper::divisionAdmin($division));

        $this->assertUserCan($divisionAdmin, "view-division-$divisionId")
            ->assertUserCan($divisionAdmin, "edit-division-$divisionId")
            ->assertUserCan($divisionAdmin, "add-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "edit-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "delete-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "view-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "view-competition-$competitionId")
            ->assertUserCan($divisionAdmin, "view-season-$seasonId");
        $this->assertUserCannot($divisionAdmin, "delete-division-$divisionId");

        $competitionAdmin = $this->userWithRole(RolesHelper::competitionAdmin($division->getCompetition()));

        $this->assertUserCan($competitionAdmin, "view-division-$divisionId")
            ->assertUserCan($competitionAdmin, "edit-division-$divisionId")
            ->assertUserCan($competitionAdmin, "add-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "edit-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "delete-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "view-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "delete-division-$divisionId");

        $seasonAdmin = $this->userWithRole(RolesHelper::seasonAdmin($division->getCompetition()->getSeason()));

        $this->assertUserCan($seasonAdmin, "view-division-$divisionId")
            ->assertUserCan($seasonAdmin, "edit-division-$divisionId")
            ->assertUserCan($seasonAdmin, "add-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "edit-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "delete-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "view-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "delete-division-$divisionId");
    }
}
