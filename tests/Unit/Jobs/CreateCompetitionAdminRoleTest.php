<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateCompetitionAdminRole;
use App\Models\Competition;
use Tests\TestCase;

class CreateCompetitionAdminRoleTest extends TestCase
{
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
