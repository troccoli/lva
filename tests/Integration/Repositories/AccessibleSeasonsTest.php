<?php

namespace Tests\Integration\Repositories;

use App\Helpers\RolesHelper;
use App\Models\Season;
use App\Models\User;
use App\Repositories\AccessibleSeasons;
use Tests\TestCase;

class AccessibleSeasonsTest extends TestCase
{
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new AccessibleSeasons();
    }

    public function testItReturnsNoSeasonsIfThereAreNone(): void
    {
        $this->assertEmpty($this->sut->get($this->siteAdmin));
    }

    public function testItReturnsAllSeasonsForSiteAdministrators(): void
    {
        factory(Season::class)->create(['year' => 2000]);
        factory(Season::class)->create(['year' => 2002]);
        factory(Season::class)->create(['year' => 2001]);

        $this->assertCount(3, $this->sut->get($this->siteAdmin));
    }

    public function testItReturnsOnlySomeSeasonsForSeasonAdministrators(): void
    {
        $season1 = factory(Season::class)->create(['year' => 2000]);
        $season2 = factory(Season::class)->create(['year' => 2002]);
        $season3 = factory(Season::class)->create(['year' => 2001]);

        $user = factory(User::class)->create();
        $user->assignRole(RolesHelper::seasonAdminName($season1));
        $user->assignRole(RolesHelper::seasonAdminName($season3));

        $this->assertCount(2, $this->sut->get($user));
    }
}
