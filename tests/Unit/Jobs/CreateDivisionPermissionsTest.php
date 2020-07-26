<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateDivisionPermissions;
use App\Models\Division;
use Tests\TestCase;

class CreateDivisionPermissionsTest extends TestCase
{
    public function testItCreatesTheDivisionPermissions(): void
    {
        $division = \Mockery::mock(Division::class, [
            'getId' => '123',
        ]);

        $sut = new CreateDivisionPermissions($division);

        $sut->handle();

        $this->assertDatabaseCount('permissions', 6);
        $this->assertDatabaseHas('permissions', ['name' => 'edit-division-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'delete-division-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'add-fixtures-in-division-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'edit-fixtures-in-division-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'delete-fixtures-in-division-123']);
        $this->assertDatabaseHas('permissions', ['name' => 'view-fixtures-in-division-123']);
    }
}
