<?php

namespace Tests\Integration\Events;

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

        $this->assertDatabaseHas('roles', ['name' => $competition->getAdminRole()]);
    }

    public function testCompetitionPermissionsAreCreated(): void
    {
        $competition = factory(Competition::class)->create();

        $this->assertDatabaseHas('permissions', ['name' => "edit-competition-{$competition->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-competition-{$competition->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-divisions-in-competition-{$competition->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-divisions-in-competition-{$competition->getId()}"]);
    }

    public function testAdminRolesHaveTheCorrectPermissions(): void
    {
        /** @var Competition $competition */
        $competition = factory(Competition::class)->create();
        $competitionId = $competition->getId();

        /** @var User $competitionAdmin */
        $competitionAdmin = factory(User::class)->create();
        $competitionAdmin->assignRole($competition->getAdminRole());

        $this->assertUserCan($competitionAdmin, "edit-competition-$competitionId")
            ->assertUserCan($competitionAdmin, "add-divisions-in-competition-$competitionId")
            ->assertUserCan($competitionAdmin, "view-divisions-in-competition-$competitionId");

        $this->assertUserCannot($competitionAdmin, "delete-competition-$competitionId");

        /** @var User $seasonAdmin */
        $seasonAdmin = factory(User::class)->create();
        $seasonAdmin->assignRole($competition->getSeason()->getAdminRole());

        $this->assertUserCan($seasonAdmin, "edit-competition-$competitionId")
            ->assertUserCan($seasonAdmin, "add-divisions-in-competition-$competitionId")
            ->assertUserCan($seasonAdmin, "view-divisions-in-competition-$competitionId");

        $this->assertUserCan($seasonAdmin, "delete-competition-$competitionId");
    }
}
