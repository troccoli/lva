<?php

namespace Tests\Integration\Repositories;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Repositories\AccessibleDivisions;
use Tests\TestCase;

class AccessibleDivisionsTest extends TestCase
{
    private AccessibleDivisions $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new AccessibleDivisions();
    }

    public function testItReturnsNoDivisionsIfThereAreNone(): void
    {
        $this->assertEmpty($this->sut->get($this->siteAdmin));
    }

    public function testItReturnsAllDivisionsForSiteAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'competition_id' => $competition2->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'competition_id' => $competition3->getId(),
        ]);

        $divisions = $this->sut->get($this->siteAdmin);

        $this->assertCount(4, $divisions);
        $this->assertTrue($divisions->pluck('id')->contains($division1->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division2->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division3->getId()));
        $this->assertTrue($divisions->pluck('id')->contains($division4->getId()));
    }

    public function testItReturnsOnlyTheDivisionsInTheSetCompetitionsForSiteAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'competition_id' => $competition2->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'competition_id' => $competition3->getId(),
        ]);

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
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'competition_id' => $competition2->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'competition_id' => $competition3->getId(),
        ]);

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
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'competition_id' => $competition2->getId(),
        ]);

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
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'competition_id' => $competition2->getId(),
        ]);

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
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);

        $club = aClub()->build();
        $clubSecretary = $this->userWithRole(RolesHelper::clubSecretary($club));

        $this->assertEmpty($this->sut->get($clubSecretary));
    }

    public function testReturnsNoDivisionsForTeamSecretaries(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);

        $team = aTeam()->build();
        $teamSecretary = $this->userWithRole(RolesHelper::teamSecretary($team));

        $this->assertEmpty($this->sut->get($teamSecretary));
    }
}
