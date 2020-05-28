<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateCompetitionAdminRole;
use App\Jobs\CreateSeasonAdminRole;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCompetitionAdminRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testItCreatesTheCompetitionAdminRole(): void
    {
        $competition = \Mockery::mock(Competition::class, [
            'getAdminRole' => 'Competition Admin Role'
        ]);

        $sut = new CreateCompetitionAdminRole($competition);

        $sut->handle();

        $this->assertDatabaseHas('roles', ['name' => 'Competition Admin Role']);
    }
}
