<?php

namespace Tests\Integration\Events;

use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSeasonAdminRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testSeasonAdminRoleIsCreatedWhenSeasonIsCreated(): void
    {
        $season = factory(Season::class)->create();

        $this->assertDatabaseHas('roles', ['name' => "Season {$season->getId()} Administrator"]);
    }

    public function testSeasonsPermissionsAreCreatedWhenSeasonIsCreated(): void
    {
        $season = factory(Season::class)->create();

        $this->assertDatabaseHas('permissions', ['name' => "edit-season-{$season->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-season-{$season->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-competitions-in-season-{$season->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-competitions-in-season-{$season->getId()}"]);
    }
}
