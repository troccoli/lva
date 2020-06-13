<?php

namespace Tests\Integration\Events;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\User;
use Tests\Concerns\InteractsWithPermissions;
use Tests\TestCase;

class CompetitionCreatedTest extends TestCase
{
    use InteractsWithPermissions;

    public function testCompetitionAdminRoleIsCreated(): void
    {
        $competition = factory(Competition::class)->create();

        $this->assertDatabaseHas('roles', ['name' => RolesHelper::competitionAdminName($competition)]);
    }

    public function testCompetitionPermissionsAreCreated(): void
    {
        $competition = factory(Competition::class)->create();

        $this->assertDatabaseHas('permissions', ['name' => "edit-competition-{$competition->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-competition-{$competition->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-division-in-competition-{$competition->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-divisions-in-competition-{$competition->getId()}"]);
    }

    public function testAdminRolesHaveTheCorrectPermissions(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $competitionId = $competition->getId();
        $seasonId = $competition->getSeason()->getId();

        /** @var User $competitionAdmin */
        $competitionAdmin = factory(User::class)->create();
        $competitionAdmin->assignRole(RolesHelper::competitionAdminName($competition));

        $this->assertUserCan($competitionAdmin, "view-competitions-in-season-$seasonId")
            ->assertUserCan($competitionAdmin, "edit-competition-$competitionId")
            ->assertUserCan($competitionAdmin, "add-division-in-competition-$competitionId")
            ->assertUserCan($competitionAdmin, "view-divisions-in-competition-$competitionId");

        $this->assertUserCannot($competitionAdmin, "delete-competition-$competitionId");

        /** @var User $seasonAdmin */
        $seasonAdmin = factory(User::class)->create();
        $seasonAdmin->assignRole(RolesHelper::seasonAdminName($competition->getSeason()));

        $this->assertUserCan($seasonAdmin, "edit-competition-$competitionId")
            ->assertUserCan($seasonAdmin, "add-division-in-competition-$competitionId")
            ->assertUserCan($seasonAdmin, "view-divisions-in-competition-$competitionId");

        $this->assertUserCan($seasonAdmin, "delete-competition-$competitionId");
    }
}
