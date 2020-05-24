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
}
