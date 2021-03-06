<?php

namespace Tests\Integration\Api\V1;

use App\Helpers\RolesHelper;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use Laravel\Passport\Passport;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class SeasonsTest extends TestCase
{
    use InteractsWithArrays;

    public function testTheSeasonsAreOrderedByYear(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $season3 = Season::factory()->create(['year' => 1999]);
        $season4 = Season::factory()->create(['year' => 1998]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons')
                         ->assertOk();

        $data = $response->json('data');
        $this->assertSame(
            [
                [
                    'id' => $season2->getId(),
                    'name' => '2001/02',
                ],
                [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                [
                    'id' => $season3->getId(),
                    'name' => '1999/00',
                ],
                [
                    'id' => $season4->getId(),
                    'name' => '1998/99',
                ],
            ],
            $data
        );
    }

    public function testGettingAllSeasonsWhenThereAreNone(): void
    {
        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    public function testGettingANonExistingSeason(): void
    {
        Passport::actingAs($this->siteAdmin);

        $this->getJson('/api/v1/seasons/1')
             ->assertNotFound();
    }

    /**********************
     * Site Administrator *
     **********************/

    /* - all seasons => all seasons */
    public function testGettingAllSeasonsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons')
                         ->assertOk();

        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertContains(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $data
        );
        $this->assertContains(
            [
                'id' => $season2->getId(),
                'name' => '2001/02',
            ],
            $data
        );
    }

    /* - all seasons with competitions => all seasons and their competitions */
    public function testGettingAllSeasonsWithCompetitionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        $competition1 = Competition::factory()->for($season1)->create();
        $competition2 = Competition::factory()->for($season1)->create();
        $competition3 = Competition::factory()->for($season2)->create();
        $competition4 = Competition::factory()->for($season2)->create();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
                         ->assertOk();

        $seasons = $response->json('data');
        $seasons = $this->keyArrayBy($seasons, 'id');

        $this->assertCount(2, $seasons);

        $season1Data = $seasons[$season1->getId()];
        $this->assertArrayContent(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $season1Data
        );
        $this->assertArrayHasKey('competitions', $season1Data);
        $season1Competitions = $season1Data['competitions'];
        $this->assertCount(2, $season1Competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => $competition1->getName(),
            ],
            $season1Competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition2->getId(),
            [
                'id' => $competition2->getId(),
                'name' => $competition2->getName(),
            ],
            $season1Competitions
        );

        $season2Data = $seasons[$season2->getId()];
        $this->assertArrayContent(
            [
                'id' => $season2->getId(),
                'name' => '2001/02',
            ],
            $season2Data
        );
        $this->assertArrayHasKey('competitions', $season2Data);
        $season2Competitions = $season2Data['competitions'];
        $this->assertCount(2, $season2Competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition3->getId(),
            [
                'id' => $competition3->getId(),
                'name' => $competition3->getName(),
            ],
            $season2Competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition4->getId(),
            [
                'id' => $competition4->getId(),
                'name' => $competition4->getName(),
            ],
            $season2Competitions
        );
    }

    /* - season 1 => only season 1 */
    public function testGettingOneSeasonAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2001]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}")
                         ->assertOk();

        $data = $response->json('data');
        $this->assertSame(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $data
        );
    }

    /* - season 1 with competitions => only season 1 and its competitions */
    public function testGettingOneSeasonWithCompetitionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        $competition1 = Competition::factory()->for($season1)->create();
        $competition2 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}?with[]=competitions")
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertArrayContent(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );
        $this->assertArrayHasKey('competitions', $seasons);
        $season1Competitions = $seasons['competitions'];
        $this->assertCount(2, $season1Competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => $competition1->getName(),
            ],
            $season1Competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition2->getId(),
            [
                'id' => $competition2->getId(),
                'name' => $competition2->getName(),
            ],
            $season1Competitions
        );
    }

    /************************
     * Season Administrator *
     ************************/

    /* - all seasons => only season 1 */
    public function testGettingAllSeasonsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2001]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/seasons')
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertCount(1, $seasons);
        $this->assertContains(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );
    }

    /* - all seasons with competitions => only season 1 its competitions */
    public function testGettingAllSeasonsWithCompetitionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        $competition1 = Competition::factory()->for($season1)->create();
        $competition2 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
                         ->assertOk();

        $seasons = $response->json('data');
        $seasons = $this->keyArrayBy($seasons, 'id');

        $this->assertCount(1, $seasons);

        $season1Data = $seasons[$season1->getId()];
        $this->assertArrayContent(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $season1Data
        );
        $this->assertArrayHasKey('competitions', $season1Data);
        $season1Competitions = $season1Data['competitions'];
        $this->assertCount(2, $season1Competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => $competition1->getName(),
            ],
            $season1Competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition2->getId(),
            [
                'id' => $competition2->getId(),
                'name' => $competition2->getName(),
            ],
            $season1Competitions
        );
    }

    /*
     * - season 1 => only season 1
     * - season 2 => no data
     */
    public function testGettingOneSeasonAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}")
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertSame(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );

        $response = $this->getJson("/api/v1/seasons/{$season2->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - season 1 with competitions => only season 1 with its competitions
     * - season 2 with competitions => no data
     */
    public function testGettingOneSeasonWithCompetitionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        $competition1 = Competition::factory()->for($season1)->create();
        $competition2 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}?with[]=competitions")
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertArrayContent(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );
        $this->assertArrayHasKey('competitions', $seasons);
        $season1Competitions = $seasons['competitions'];
        $this->assertCount(2, $season1Competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => $competition1->getName(),
            ],
            $season1Competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition2->getId(),
            [
                'id' => $competition2->getId(),
                'name' => $competition2->getName(),
            ],
            $season1Competitions
        );

        $response = $this->getJson("/api/v1/seasons/{$season2->getId()}?with[]=competitions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*****************************
     * Competition Administrator *
     *****************************/

    /* - all seasons => only season 1 */
    public function testGettingAllSeasonsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/seasons')
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertCount(1, $seasons);
        $this->assertContains(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );
    }

    /* - all seasons with competitions => only season 1 with competition 1 */
    public function testGettingAllSeasonsWithCompetitionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
                         ->assertOk();

        $seasons = $response->json('data');
        $seasons = $this->keyArrayBy($seasons, 'id');

        $this->assertCount(1, $seasons);

        $season1Data = $seasons[$season1->getId()];
        $this->assertArrayContent(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $season1Data
        );
        $this->assertArrayHasKey('competitions', $season1Data);
        $season1Competitions = $season1Data['competitions'];
        $this->assertCount(1, $season1Competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => $competition1->getName(),
            ],
            $season1Competitions
        );
    }

    /*
     * - season 1 => only season 1
     * - season 2 => no data
     */
    public function testGettingOneSeasonAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}")
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertSame(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );

        $response = $this->getJson("/api/v1/seasons/{$season2->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - season 1 with competitions => only season 1 with competition 1
     * - season 2 with competitions => no data
     */
    public function testGettingOneSeasonWithCompetitionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}?with[]=competitions")
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertArrayContent(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );
        $this->assertArrayHasKey('competitions', $seasons);
        $season1Competitions = $seasons['competitions'];
        $this->assertCount(1, $season1Competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => $competition1->getName(),
            ],
            $season1Competitions
        );

        $response = $this->getJson("/api/v1/seasons/{$season2->getId()}?with[]=competitions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /**************************
     * Division Administrator *
     **************************/

    /* - all season => only season 1 */
    public function testGettingAllSeasonsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        $competition1 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/seasons')
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertCount(1, $seasons);
        $this->assertContains(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );
    }

    /* - all seasons with competitions => only season 1 with competition 1 */
    public function testGettingAllSeasonsWithCompetitionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        $competition1 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
                         ->assertOk();

        $seasons = $response->json('data');
        $seasons = $this->keyArrayBy($seasons, 'id');

        $this->assertCount(1, $seasons);

        $season1Data = $seasons[$season1->getId()];
        $this->assertArrayContent(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $season1Data
        );
        $this->assertArrayHasKey('competitions', $season1Data);
        $season1Competitions = $season1Data['competitions'];
        $this->assertCount(1, $season1Competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => $competition1->getName(),
            ],
            $season1Competitions
        );
    }

    /*
     * - season 1 => only season 1
     * - season 2 => no data
     */
    public function testGettingOneSeasonAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        $competition1 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}")
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertSame(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );

        $response = $this->getJson("/api/v1/seasons/{$season2->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - season 1 with competitions => only season 1 with competition 1
     * - season 2 with competitions => no data
     */
    public function testGettingOneSeasonWithCompetitionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        $competition1 = Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}?with[]=competitions")
                         ->assertOk();

        $seasons = $response->json('data');
        $this->assertArrayContent(
            [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
            $seasons
        );
        $this->assertArrayHasKey('competitions', $seasons);
        $season1Competitions = $seasons['competitions'];
        $this->assertCount(1, $season1Competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => $competition1->getName(),
            ],
            $season1Competitions
        );

        $response = $this->getJson("/api/v1/seasons/{$season2->getId()}?with[]=competitions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /******************
     * Club Secretary *
     ******************/

    /* - all seasons => no data */
    public function testGettingAllSeasonsAsClubSecretary(): void
    {
        Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2001]);

        /** @var Club $club */
        $club = Club::factory()->create();

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/seasons')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - all seasons with competitions => no data */
    public function testGettingAllSeasonsWithCompetitionsAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        /** @var Club $club */
        $club = Club::factory()->create();

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - season 1 => no data */
    public function testGettingOneSeasonAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2001]);

        /** @var Club $club */
        $club = Club::factory()->create();

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - season 1 with competitions => no data */
    public function testGettingOneSeasonWithCompetitionsAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2001]);

        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();

        /** @var Club $club */
        $club = Club::factory()->create();

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}?with[]=competitions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /******************
     * Team Secretary *
     ******************/

    /* - all seasons => no data */
    public function testGettingAllSeasonsAsTeamSecretary(): void
    {
        Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2001]);

        /** @var Team $team */
        $team = Team::factory()->create();

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/seasons')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - all seasons with competitions => no data */
    public function testGettingAllSeasonsWithCompetitionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $season2 = Season::factory()->create(['year' => 2001]);

        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season2)->create();
        Competition::factory()->for($season2)->create();

        /** @var Team $team */
        $team = Team::factory()->create();

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - season 1 => no data */
    public function testGettingOneSeasonAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2001]);

        /** @var Team $team */
        $team = Team::factory()->create();

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - season 1 => no data */
    public function testGettingOneSeasonWithCompetitionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Season::factory()->create(['year' => 2001]);

        Competition::factory()->for($season1)->create();
        Competition::factory()->for($season1)->create();

        /** @var Team $team */
        $team = Team::factory()->create();

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson("/api/v1/seasons/{$season1->getId()}?with[]=competitions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }
}
