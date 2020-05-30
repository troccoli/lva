<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateTeamSecretaryRole;
use App\Models\Team;
use Tests\TestCase;

class CreateTeamSecretaryRoleTest extends TestCase
{
    public function testItCreatesTheTeamAdminRole(): void
    {
        $team = \Mockery::mock(Team::class, [
            'getSecretaryRole' => 'Team Secretary Role',
        ]);

        $sut = new CreateTeamSecretaryRole($team);

        $sut->handle();

        $this->assertDatabaseHas('roles', ['name' => 'Team Secretary Role']);
    }
}
