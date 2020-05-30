<?php

namespace Tests\Integration\Events;

use App\Models\Competition;
use Tests\TestCase;

class CompetitionCreatedTest extends TestCase
{
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
}
