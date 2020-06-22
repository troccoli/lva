<?php

namespace Tests\Integration\Repositories;

use App\Helpers\PermissionsHelper;
use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use App\Repositories\AccessibleCompetitions;
use Tests\TestCase;

class AccessibleCompetitionsTest extends TestCase
{
    private AccessibleCompetitions $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new AccessibleCompetitions();
    }

    public function testItReturnsNoCompetitionsIfThereAreNone(): void
    {
        $this->assertEmpty($this->sut->get($this->siteAdmin));
    }

    public function testItReturnsCompetitionsDependingOnPermissions(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2002]);
        factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);

        /** @var User $season1Admin */
        $season1Admin = factory(User::class)->create();
        $season1Admin->givePermissionTo(PermissionsHelper::viewCompetitions($season1));
        $this->assertCount(2, $this->sut->get($season1Admin));

        /** @var User $season2Admin */
        $season2Admin = factory(User::class)->create();
        $season2Admin->givePermissionTo(PermissionsHelper::viewCompetitions($season2));
        $this->assertCount(1, $this->sut->get($season2Admin));

        /** @var User $competition2Admin */
        $competition2Admin = factory(User::class)->create();
        $competition2Admin->givePermissionTo(PermissionsHelper::viewCompetition($competition2));
        $this->assertCount(1, $this->sut->get($competition2Admin));
    }

    public function testItCanScopeTheCompetitionsBySeason(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2002]);
        factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);

        /** @var User $season1Admin */
        $season1Admin = factory(User::class)->create();
        $season1Admin->givePermissionTo(PermissionsHelper::viewCompetitions($season1));
        $this->assertCount(2, $this->sut->inSeason($season1)->get($season1Admin));
        $this->assertEmpty($this->sut->inSeason($season2)->get($season1Admin));

        /** @var User $season2Admin */
        $season2Admin = factory(User::class)->create();
        $season2Admin->givePermissionTo(PermissionsHelper::viewCompetitions($season2));
        $this->assertEmpty($this->sut->inSeason($season1)->get($season2Admin));
        $this->assertCount(1, $this->sut->inSeason($season2)->get($season2Admin));

        /** @var User $competition2Admin */
        $competition2Admin = factory(User::class)->create();
        $competition2Admin->givePermissionTo(PermissionsHelper::viewCompetition($competition2));
        $this->assertCount(1, $this->sut->inSeason($season1)->get($competition2Admin));
        $this->assertEmpty($this->sut->inSeason($season2)->get($competition2Admin));
    }
}
