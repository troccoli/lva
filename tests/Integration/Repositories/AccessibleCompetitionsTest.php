<?php

namespace Tests\Integration\Repositories;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
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

    public function testItReturnsAllCompetitionsForSiteAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2002]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'season_id' => $season2->getId(),
        ]);

        $competitions = $this->sut->get($this->siteAdmin);

        $this->assertCount(3, $competitions);
        $this->assertTrue($competitions->pluck('id')->contains($competition1->getId()));
        $this->assertTrue($competitions->pluck('id')->contains($competition2->getId()));
        $this->assertTrue($competitions->pluck('id')->contains($competition3->getId()));
    }

    public function testItReturnsOnlyTheCompetitionsInTheSetSeasonForSiteAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2002]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'season_id' => $season2->getId(),
        ]);

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
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2002]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'season_id' => $season2->getId(),
        ]);

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
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2002]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'season_id' => $season2->getId(),
        ]);

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
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'competition_id' => $competition1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2002]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'season_id' => $season2->getId(),
        ]);

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
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);

        $club = aClub()->build();
        $clubSecretary = $this->userWithRole(RolesHelper::clubSecretary($club));

        $this->assertEmpty($this->sut->get($clubSecretary));
    }

    public function testReturnsNoCompetitionsForTeamSecretaries(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'season_id' => $season1->getId(),
        ]);

        $team = aTeam()->build();
        $teamSecretary = $this->userWithRole(RolesHelper::teamSecretary($team));

        $this->assertEmpty($this->sut->get($teamSecretary));
    }
}
