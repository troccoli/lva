<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateClubPermissions;
use App\Models\Club;
use Tests\TestCase;

class CreateClubPermissionsTest extends TestCase
{
    public function testItCreatesTheClubPermissions(): void
    {
        $club = \Mockery::mock(Club::class, [
            'getId' => '123',
        ]);

        $sut = new CreateClubPermissions($club);

        $sut->handle();

        $this->assertDatabaseCount('permissions', 4);
        $this->assertDatabaseHas('permissions', ['name' => 'edit-club-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'delete-club-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'add-team-in-club-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'view-teams-in-club-123']);
    }
}
