<?php

namespace Tests\Integration\Events;

use App\Events\CompetitionCreated;
use App\Models\Competition;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CreateDivisionAdminRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testDivisionAdminRoleIsCreatedWhenDivisionIsCreated(): void
    {
        $division = factory(Division::class)->create();

        $this->assertDatabaseHas('roles', ['name' => "Division {$division->getId()} Administrator"]);
    }
}
