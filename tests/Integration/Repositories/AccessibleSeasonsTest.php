<?php

namespace Tests\Integration\Repositories;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use App\Repositories\AccessibleSeasons;
use Tests\TestCase;

class AccessibleSeasonsTest extends TestCase
{
    private $sut;

    public function testItReturnsNoSeasonsIfThereAreNone(): void
    {
        $this->assertEmpty($this->sut->get($this->siteAdmin));
    }

    public function testItReturnsAllSeasonsForSiteAdministrators(): void
    {
        Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2002]);
        Season::factory()->create(['year' => 2001]);

        $seasons = $this->sut->get($this->siteAdmin);

        $this->assertCount(3, $seasons);
        $this->assertTrue($seasons->pluck('year')->contains('2000'));
        $this->assertTrue($seasons->pluck('year')->contains('2001'));
        $this->assertTrue($seasons->pluck('year')->contains('2002'));
    }

    public function testItReturnsOnlyOneSeasonsForSeasonAdministrators(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2002]);
        Season::factory()->create(['year' => 2001]);

        $season1Admin = $this->userWithRole(RolesHelper::seasonAdmin($season1));

        $seasons = $this->sut->get($season1Admin);
        $this->assertCount(1, $seasons);
        $this->assertTrue($seasons->pluck('year')->contains('2000'));
    }

    public function testItReturnsOnlyOneSeasonForCompetitionAdministrators(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        Season::factory()->create(['year' => 2002]);
        Season::factory()->create(['year' => 2001]);

        $competition1Admin = $this->userWithRole(RolesHelper::competitionAdmin($competition1));

        $seasons = $this->sut->get($competition1Admin);
        $this->assertCount(1, $seasons);
        $this->assertTrue($seasons->pluck('year')->contains('2000'));
    }

    public function testItReturnsOnlyOneSeasonForDivisionAdministrators(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create();
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create();
        Season::factory()->create(['year' => 2002]);
        Season::factory()->create(['year' => 2001]);

        $division1Admin = $this->userWithRole(RolesHelper::divisionAdmin($division1));

        $seasons = $this->sut->get($division1Admin);
        $this->assertCount(1, $seasons);
        $this->assertTrue($seasons->pluck('year')->contains('2000'));
    }

    public function testItReturnsNoSeasonsForClubSecretaries(): void
    {
        Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2002]);
        Season::factory()->create(['year' => 2001]);

        /** @var Club $club */
        $club = Club::factory()->create();
        $clubSecretary = $this->userWithRole(RolesHelper::clubSecretary($club));

        $this->assertEmpty($this->sut->get($clubSecretary));
    }

    public function testItReturnsNoSeasonsForTeamSecretaries(): void
    {
        Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2002]);
        Season::factory()->create(['year' => 2001]);

        /** @var Team $team */
        $team = Team::factory()->create();
        $teamSecretary = $this->userWithRole(RolesHelper::teamSecretary($team));

        $this->assertEmpty($this->sut->get($teamSecretary));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new AccessibleSeasons();
    }
}
