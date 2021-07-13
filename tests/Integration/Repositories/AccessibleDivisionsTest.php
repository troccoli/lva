<?php

namespace Tests\Integration\Repositories;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use App\Repositories\AccessibleDivisions;
use Tests\TestCase;

class AccessibleDivisionsTest extends TestCase
{
    private AccessibleDivisions $sut;

    public function testItReturnsNoDivisionsIfThereAreNone(): void
    {
        $this->assertEmpty($this->sut->get($this->siteAdmin));
    }

    public function testItReturnsAllDivisionsForSiteAdministrators(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create();
        $division1 = Division::factory()->for($competition1)->create();
        $division2 = Division::factory()->for($competition1)->create();
        $competition2 = Competition::factory()->for($season1)->create();
        $division3 = Division::factory()->for($competition2)->create();
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create();
        $division4 = Division::factory()->for($competition3)->create();

        $divisions = $this->sut->get($this->siteAdmin);

        $this->assertCount(4, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division1->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division2->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division3->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division4->getId()));
    }

    public function testItReturnsOnlyTheDivisionsInTheSetCompetitionsForSiteAdministrators(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        $division1 = Division::factory()->for($competition1)->create();
        $division2 = Division::factory()->for($competition1)->create();
        /** @var Competition $competition2 */
        $competition2 = Competition::factory()->for($season1)->create();
        $division3 = Division::factory()->for($competition2)->create();
        $season2 = Season::factory()->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = Competition::factory()->for($season2)->create();
        $division4 = Division::factory()->for($competition3)->create();

        $divisions = $this->sut->inCompetition($competition1)->get($this->siteAdmin);

        $this->assertCount(2, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division1->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division2->getId()));

        $divisions = $this->sut->inCompetition($competition2)->get($this->siteAdmin);

        $this->assertCount(1, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division3->getId()));

        $divisions = $this->sut->inCompetition($competition3)->get($this->siteAdmin);

        $this->assertCount(1, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division4->getId()));
    }

    public function testItReturnsSomeDivisionsForSeasonAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        $division1 = Division::factory()->for($competition1)->create();
        $division2 = Division::factory()->for($competition1)->create();
        /** @var Competition $competition2 */
        $competition2 = Competition::factory()->for($season1)->create();
        $division3 = Division::factory()->for($competition2)->create();
        $season2 = Season::factory()->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = Competition::factory()->for($season2)->create();
        Division::factory()->for($competition3)->create();

        $season1Admin = $this->userWithRole(RolesHelper::seasonAdmin($season1));

        $divisions = $this->sut->get($season1Admin);
        $this->assertCount(3, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division1->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division2->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division3->getId()));

        $divisions = $this->sut->inCompetition($competition1)->get($season1Admin);
        $this->assertCount(2, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division1->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division2->getId()));

        $divisions = $this->sut->inCompetition($competition2)->get($season1Admin);
        $this->assertCount(1, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division3->getId()));

        $divisions = $this->sut->inCompetition($competition3)->get($season1Admin);
        $this->assertEmpty($divisions);
    }

    public function testItReturnsSomeDivisionsForCompetitionAdministrators(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        $division1 = Division::factory()->for($competition1)->create();
        $division2 = Division::factory()->for($competition1)->create();
        /** @var Competition $competition2 */
        $competition2 = Competition::factory()->for($season1)->create();
        Division::factory()->for($competition2)->create();

        $competition1Admin = $this->userWithRole(RolesHelper::competitionAdmin($competition1));

        $divisions = $this->sut->get($competition1Admin);
        $this->assertCount(2, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division1->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division2->getId()));

        $divisions = $this->sut->inCompetition($competition1)->get($competition1Admin);
        $this->assertCount(2, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division1->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division2->getId()));

        $divisions = $this->sut->inCompetition($competition2)->get($competition1Admin);
        $this->assertEmpty($divisions);
    }

    public function testItReturnsOneDivisionForDivisionAdministrators(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create();
        Division::factory()->for($competition1)->create();
        /** @var Competition $competition2 */
        $competition2 = Competition::factory()->for($season1)->create();
        Division::factory()->for($competition2)->create();

        $division1Admin = $this->userWithRole(RolesHelper::divisionAdmin($division1));

        $divisions = $this->sut->get($division1Admin);
        $this->assertCount(1, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division1->getId()));

        $divisions = $this->sut->inCompetition($competition1)->get($division1Admin);
        $this->assertCount(1, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division1->getId()));

        $divisions = $this->sut->inCompetition($competition2)->get($division1Admin);
        $this->assertEmpty($divisions);
    }

    public function testReturnsNoDivisionsForClubSecretaries(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create();
        Division::factory()->for($competition1)->create();

        /** @var Club $club */
        $club = Club::factory()->create();
        $clubSecretary = $this->userWithRole(RolesHelper::clubSecretary($club));

        $this->assertEmpty($this->sut->get($clubSecretary));
    }

    public function testReturnsNoDivisionsForTeamSecretaries(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create();
        Division::factory()->for($competition1)->create();

        /** @var Team $team */
        $team = Team::factory()->create();
        $teamSecretary = $this->userWithRole(RolesHelper::teamSecretary($team));

        $this->assertEmpty($this->sut->get($teamSecretary));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new AccessibleDivisions();
    }
}
