<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateSeasonAdminRole;
use App\Models\Season;
use Tests\TestCase;

class CreateSeasonAdminRoleTest extends TestCase
{
    public function testItCreatesTheSeasonAdminRole(): void
    {
        $season = \Mockery::mock(
            Season::class,
            [
                'getId' => '111',
            ]
        );

        $sut = new CreateSeasonAdminRole($season);

        $sut->handle();

        $this->assertDatabaseHas('roles', ['name' => 'Season 111 Administrator']);
    }
}
