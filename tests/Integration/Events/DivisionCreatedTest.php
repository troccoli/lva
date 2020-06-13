<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Division;
use App\Models\User;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class DivisionCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testDivisionAdminRoleIsCreated(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::divisionAdminName($division)]);
    }

    public function testDivisionPermissionsAreCreated(): void
    {
        $division = factory(Division::class)->create();

        $this->assertDatabaseHas('permissions', ['name' => "edit-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-fixtures-in-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "edit-fixtures-in-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-fixtures-in-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-fixtures-in-division-{$division->getId()}"]);
    }

    public function testAdminRolesHaveTheCorrectPermissions(): void
    {
        /** @var Division $division */
        $division = factory(Division::class)->create();
        $divisionId = $division->getId();
        $competitionId = $division->getCompetition()->getId();

        /** @var User $divisionAdmin */
        $divisionAdmin = factory(User::class)->create();
        $divisionAdmin->assignRole(RolesHelper::divisionAdminName($division));

        $this->assertUserCan($divisionAdmin, "view-divisions-in-competition-$competitionId")
            ->assertUserCan($divisionAdmin, "edit-division-$divisionId")
            ->assertUserCan($divisionAdmin, "add-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "edit-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "delete-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "view-fixtures-in-division-$divisionId");

        $this->assertUserCannot($divisionAdmin, "delete-division-$divisionId");

        /** @var User $competitionAdmin */
        $competitionAdmin = factory(User::class)->create();
        $competitionAdmin->assignRole(RolesHelper::competitionAdminName($division->getCompetition()));

        $this->assertUserCan($competitionAdmin, "edit-division-$divisionId")
            ->assertUserCan($competitionAdmin, "add-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "edit-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "delete-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "view-fixtures-in-division-$divisionId");

        $this->assertUserCan($competitionAdmin, "delete-division-$divisionId");

        /** @var User $seasonAdmin */
        $seasonAdmin = factory(User::class)->create();
        $seasonAdmin->assignRole(RolesHelper::seasonAdminName($division->getCompetition()->getSeason()));

        $this->assertUserCan($seasonAdmin, "edit-division-$divisionId")
            ->assertUserCan($seasonAdmin, "add-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "edit-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "delete-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "view-fixtures-in-division-$divisionId");

        $this->assertUserCan($seasonAdmin, "delete-division-$divisionId");
    }
}
