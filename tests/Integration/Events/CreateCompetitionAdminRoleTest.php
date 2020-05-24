<?php

namespace Tests\Integration\Events;

use App\Events\CompetitionCreated;
use App\Models\Competition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCompetitionAdminRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testCompetitionAdminRoleIsCreatedWhenCompetitionIsCreated(): void
    {
        $competition = factory(Competition::class)->create();

        $this->assertDatabaseHas('roles', ['name' => "Competition {$competition->getId()} Administrator"]);
    }
}
