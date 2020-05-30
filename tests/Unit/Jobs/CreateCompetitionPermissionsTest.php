<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateCompetitionPermissions;
use App\Models\Competition;
use Tests\TestCase;

class CreateCompetitionPermissionsTest extends TestCase
{
    public function testItCreatesTheCompetitionPermissions(): void
    {
        $competition = \Mockery::mock(Competition::class, [
            'getId' => '123',
        ]);

        $sut = new CreateCompetitionPermissions($competition);

        $sut->handle();

        $this->assertDatabaseCount('permissions', 5);
        $this->assertDatabaseHas('permissions', ['name' => 'edit-competition-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'delete-competition-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'add-divisions-in-competition-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'view-divisions-in-competition-123']);
    }
}
