<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CreateDivisionAdminRole;
use App\Jobs\CreateSeasonAdminRole;
use App\Models\Division;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDivisionAdminRoleTest extends TestCase
{
    use RefreshDatabase;

    public function testItCreatesTheDivisionAdminRole(): void
    {
        $division = \Mockery::mock(Division::class, [
            'getAdminRole' => 'Division Admin Role'
        ]);

        $sut = new CreateDivisionAdminRole($division);

        $sut->handle();

        $this->assertDatabaseHas('roles', ['name' => 'Division Admin Role']);
    }
}
