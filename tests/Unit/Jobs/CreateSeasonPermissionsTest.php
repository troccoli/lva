<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateSeasonPermissions;
use App\Models\Season;
use Tests\TestCase;

class CreateSeasonPermissionsTest extends TestCase
{
    public function testItCreatesTheSeasonPermissions(): void
    {
        $season = \Mockery::mock(Season::class, [
            'getId' => '123',
        ]);

        $sut = new CreateSeasonPermissions($season);

        $sut->handle();

        /** The count includes the default 'view-seasons' permission */
        $this->assertDatabaseCount('permissions', 4);
        $this->assertDatabaseHas('permissions', ['name' => 'edit-season-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'delete-season-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'add-competition-in-season-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'view-competitions-in-season-123']);
    }
}
