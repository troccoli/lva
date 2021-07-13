<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateTeamPermissions;
use App\Models\Team;
use Tests\TestCase;

class CreateTeamPermissionsTest extends TestCase
{
    public function testItCreatesTheClubPermissions(): void
    {
        $team = \Mockery::mock(
            Team::class,
            [
                'getId' => '123',
            ]
        );

        $sut = new CreateTeamPermissions($team);

        $sut->handle();

        $this->assertDatabaseCount('permissions', 3);
        $this->assertDatabaseHas('permissions', ['name' => 'view-team-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'edit-team-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'delete-team-123']);
    }
}
