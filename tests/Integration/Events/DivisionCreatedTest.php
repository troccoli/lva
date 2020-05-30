<?php

namespace Tests\Integration\Events;

use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DivisionCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function testDivisionAdminRoleIsCreated(): void
    {
        $division = factory(Division::class)->create();

        $this->assertDatabaseHas('roles', ['name' => $division->getAdminRole()]);
    }

    public function testDivisionPermissionsAreCreated(): void
    {
        $division = factory(Division::class)->create();

        $this->assertDatabaseHas('permissions', ['name' => "edit-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-fixtures-in-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "edit-fixtures-in-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-fixtures-in-division-{$division->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-fixtures-in-division-{$division->getId()}"]);
    }
}
