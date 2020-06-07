<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateClubSecretaryRole;
use App\Models\Club;
use Tests\TestCase;

class CreateClubSecretaryRoleTest extends TestCase
{
    public function testItCreatesTheClubAdminRole(): void
    {
        $club = \Mockery::mock(Club::class, [
            'getId' => '357',
        ]);

        $sut = new CreateClubSecretaryRole($club);

        $sut->handle();

        $this->assertDatabaseHas('roles', ['name' => 'Club 357 Secretary']);
    }
}
