<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateSeasonPermissions;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSeasonPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function testItCreatesTheSeasonPermissions(): void
    {
        $season = \Mockery::mock(Season::class, [
            'getId' => '123',
        ]);

        $sut = new CreateSeasonPermissions($season);

        $sut->handle();

        $this->assertDatabaseCount('permissions', 5);
        $this->assertDatabaseHas('permissions', ['name' => 'edit-season-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'delete-season-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'add-competitions-in-season-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'view-competitions-in-season-123']);
    }
}
