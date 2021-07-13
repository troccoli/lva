<?php

namespace Tests\Integration\Repositories;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use App\Repositories\AccessibleCompetitions;
use Tests\TestCase;

class AccessibleCompetitionsTest extends TestCase
{
    private AccessibleCompetitions $sut;

    public function testItReturnsNoCompetitionsIfThereAreNone(): void
    {
        $this->assertEmpty($this->sut->get($this->siteAdmin));
    }

    public function testItReturnsAllCompetitionsForSiteAdministrators(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create();
        $competition2 = Competition::factory()->for($season1)->create();
        $season2 = Season::factory()->create(['year' => 2002]);
        $competition3 = Competition::factory()->for($season2)->create();

        $competitions = $this->sut->get($this->siteAdmin);

        $this->assertCount(3, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition1->getId()));
        $this->assertTrue($competitions->pluck('id')->contains($competition2->getId()));
        $this->assertTrue($competitions->pluck('id')->contains($competition3->getId()));
    }

    public function testItReturnsOnlyTheCompetitionsInTheSetSeasonForSiteAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create();
        $competition2 = Competition::factory()->for($season1)->create();
        /** @var Season $season2 */
        $season2 = Season::factory()->create(['year' => 2002]);
        $competition3 = Competition::factory()->for($season2)->create();

        $competitions = $this->sut->inSeason($season1)->get($this->siteAdmin);

        $this->assertCount(2, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition1->getId()));
        $this->assertTrue($competitions->pluck('id')->contains($competition2->getId()));

        $competitions = $this->sut->inSeason($season2)->get($this->siteAdmin);

        $this->assertCount(1, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition3->getId()));
    }

    public function testItReturnsSomeCompetitionsForSeasonAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create();
        $competition2 = Competition::factory()->for($season1)->create();
        /** @var Season $season2 */
        $season2 = Season::factory()->create(['year' => 2002]);
        Competition::factory()->for($season2)->create();

        $season1Admin = $this->userWithRole(RolesHelper::seasonAdmin($season1));

        $competitions = $this->sut->get($season1Admin);
        $this->assertCount(2, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition1->getId()));
        $this->assertTrue($competitions->pluck('id')->contains($competition2->getId()));

        $competitions = $this->sut->inSeason($season1)->get($season1Admin);
        $this->assertCount(2, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition1->getId()));
        $this->assertTrue($competitions->pluck('id')->contains($competition2->getId()));

        $competitions = $this->sut->inSeason($season2)->get($season1Admin);
        $this->assertEmpty($competitions);
    }

    public function testItReturnsOneCompetitionForCompetitionAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        /** @var Season $season2 */
        $season2 = Season::factory()->create(['year' => 2002]);
        Competition::factory()->for($season2)->create();

        $competition1Admin = $this->userWithRole(RolesHelper::competitionAdmin($competition1));

        $competitions = $this->sut->get($competition1Admin);
        $this->assertCount(1, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition1->getId()));

        $competitions = $this->sut->inSeason($season1)->get($competition1Admin);
        $this->assertCount(1, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition1->getId()));

        $competitions = $this->sut->inSeason($season2)->get($competition1Admin);
        $this->assertEmpty($competitions);
    }

    public function testItReturnsOneCompetitionForDivisionAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create();
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create();
        Competition::factory()->for($season1)->create();
        /** @var Season $season2 */
        $season2 = Season::factory()->create(['year' => 2002]);
        Competition::factory()->for($season2)->create();

        $division1Admin = $this->userWithRole(RolesHelper::divisionAdmin($division1));

        $competitions = $this->sut->get($division1Admin);
        $this->assertCount(1, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition1->getId()));

        $competitions = $this->sut->inSeason($season1)->get($division1Admin);
        $this->assertCount(1, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition1->getId()));

        $competitions = $this->sut->inSeason($season2)->get($division1Admin);
        $this->assertEmpty($competitions);
    }

    public function testReturnsNoCompetitionsForClubSecretaries(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Competition::factory()->for($season1)->create();

        /** @var Club $club */
        $club = Club::factory()->create();
        $clubSecretary = $this->userWithRole(RolesHelper::clubSecretary($club));

        $this->assertEmpty($this->sut->get($clubSecretary));
    }

    public function testReturnsNoCompetitionsForTeamSecretaries(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Competition::factory()->for($season1)->create();

        /** @var Team $team */
        $team = Team::factory()->create();
        $teamSecretary = $this->userWithRole(RolesHelper::teamSecretary($team));

        $this->assertEmpty($this->sut->get($teamSecretary));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new AccessibleCompetitions();
    }
}
