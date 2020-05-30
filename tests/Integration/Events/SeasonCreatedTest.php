<?php

namespace Tests\Integration\Events;

use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeasonCreatedTest extends TestCase
{
    use RefreshDatabase;

    public function testSeasonAdminRoleIsCreated(): void
    {
        $season = factory(Season::class)->create();

        $this->assertDatabaseHas('roles', ['name' => "Season {$season->getId()} Administrator"]);
    }

    public function testSeasonsPermissionsAreCreated(): void
    {
        $season = factory(Season::class)->create();

        $this->assertDatabaseHas('permissions', ['name' => "edit-season-{$season->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "delete-season-{$season->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "add-competitions-in-season-{$season->getId()}"]);
        $this->assertDatabaseHas('permissions', ['name' => "view-competitions-in-season-{$season->getId()}"]);
    }

}
