<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateDivisionAdminRole;
use App\Models\Division;
use Tests\TestCase;

class CreateDivisionAdminRoleTest extends TestCase
{
    public function testItCreatesTheDivisionAdminRole(): void
    {
        $division = \Mockery::mock(
            Division::class,
            [
                'getId' => '246',
            ]
        );

        $sut = new CreateDivisionAdminRole($division);

        $sut->handle();

        $this->assertDatabaseHas('roles', ['name' => 'Division 246 Administrator']);
    }
}
