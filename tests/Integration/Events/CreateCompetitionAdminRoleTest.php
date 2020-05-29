<?php

namespace Tests\Integration\Events;

use App\Events\CompetitionCreated;
use App\Models\Competition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCompetitionAdminRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testCompetitionAdminRoleIsCreatedWhenCompetitionIsCreated(): void
    {
        $competition = factory(Competition::class)->create();

        $this->assertDatabaseHas('roles', ['name' => $competition->getAdminRole()]);
    }

    public function testCompetitionPermissionsAreCreatedWhenCompetitionIsCreated(): void
    {
        $competition = factory(Competition::class)->create();

        $this->assertDatabaseHas('permissions', ['name' => "edit-competition-{$competition->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-competition-{$competition->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-divisions-in-competition-{$competition->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-divisions-in-competition-{$competition->getId()}"]);
    }
}
