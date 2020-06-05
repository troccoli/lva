<?php

namespace Tests\Integration\Events;

use App\Models\Division;
use App\Models\User;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class DivisionCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testDivisionAdminRoleIsCreated(): void
    {
        $division = factory(Division::class)->create();

        $this->assertDatabaseHas('roles', ['name' => $division->getAdminRole()]);
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

        /** @var User $divisionAdmin */
        $divisionAdmin = factory(User::class)->create();
        $divisionAdmin->assignRole($division->getAdminRole());

        $this->assertUserCan($divisionAdmin, "edit-division-$divisionId")
            ->assertUserCan($divisionAdmin, "add-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "edit-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "delete-fixtures-in-division-$divisionId")
            ->assertUserCan($divisionAdmin, "view-fixtures-in-division-$divisionId");

        $this->assertUserCannot($divisionAdmin, "delete-division-$divisionId");

        /** @var User $competitionAdmin */
        $competitionAdmin = factory(User::class)->create();
        $competitionAdmin->assignRole($division->getCompetition()->getAdminRole());

        $this->assertUserCan($competitionAdmin, "edit-division-$divisionId")
            ->assertUserCan($competitionAdmin, "add-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "edit-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "delete-fixtures-in-division-$divisionId")
            ->assertUserCan($competitionAdmin, "view-fixtures-in-division-$divisionId");

        $this->assertUserCan($competitionAdmin, "delete-division-$divisionId");

        /** @var User $seasonAdmin */
        $seasonAdmin = factory(User::class)->create();
        $seasonAdmin->assignRole($division->getCompetition()->getSeason()->getAdminRole());

        $this->assertUserCan($seasonAdmin, "edit-division-$divisionId")
            ->assertUserCan($seasonAdmin, "add-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "edit-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "delete-fixtures-in-division-$divisionId")
            ->assertUserCan($seasonAdmin, "view-fixtures-in-division-$divisionId");

        $this->assertUserCan($seasonAdmin, "delete-division-$divisionId");
    }
}
