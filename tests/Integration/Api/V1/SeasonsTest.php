<?php

namespace Tests\Integration\Api\V1;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use Laravel\Passport\Passport;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class SeasonsTest extends TestCase
{
    use InteractsWithArrays;

    public function testTheSeasonsAreOrderedByYear(): void
    {
        $season1 = factory(Season::class)->create(['year' => 2000]);
        $season2 = factory(Season::class)->create(['year' => 2001]);
        $season3 = factory(Season::class)->create(['year' => 1999]);
        $season4 = factory(Season::class)->create(['year' => 1998]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertSame([
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
        ], $data);
    }

    public function testGettingAllSeasonsWhenThereAreNone(): void
    {
        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
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
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);
        $this->assertContains([
            'id' => $season2->getId(),
            'name' => '2001/02',
        ], $data);
    }

    /* - all seasons with competitions => all seasons and their competitions */
    public function testGettingAllSeasonsWithCompetitionsAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        /** @var Competition $competition2_season1 */
        $competition2_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        /** @var Competition $competition1_season2 */
        $competition1_season2 = factory(Competition::class)->create(['season_id' => $season2->getId()]);
        /** @var Competition $competition2_season2 */
        $competition2_season2 = factory(Competition::class)->create(['season_id' => $season2->getId()]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $data = $this->keyArrayBy($data, 'id');

        $this->assertCount(2, $data);

        $season1Data = $data[$season1->getId()];
        $this->assertArrayContent([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $season1Data);
        $this->assertArrayHasKey('competitions', $season1Data);
        $season1Competitions = $season1Data['competitions'];
        $this->assertCount(2, $season1Competitions);
        $this->assertArrayContentByKey('id', $competition1_season1->getId(), [
            'id' => $competition1_season1->getId(),
            'name' => $competition1_season1->getName(),
        ], $season1Competitions);
        $this->assertArrayContentByKey('id', $competition2_season1->getId(), [
            'id' => $competition2_season1->getId(),
            'name' => $competition2_season1->getName(),
        ], $season1Competitions);

        $season2Data = $data[$season2->getId()];
        $this->assertArrayContent([
            'id' => $season2->getId(),
            'name' => '2001/02',
        ], $season2Data);
        $this->assertArrayHasKey('competitions', $season2Data);
        $season2Competitions = $season2Data['competitions'];
        $this->assertCount(2, $season2Competitions);
        $this->assertArrayContentByKey('id', $competition1_season2->getId(), [
            'id' => $competition1_season2->getId(),
            'name' => $competition1_season2->getName(),
        ], $season2Competitions);
        $this->assertArrayContentByKey('id', $competition2_season2->getId(), [
            'id' => $competition2_season2->getId(),
            'name' => $competition2_season2->getName(),
        ], $season2Competitions);
    }

    /* - season 1 => only season 1 */
    public function testGettingOneSeasonAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        factory(Season::class)->create(['year' => 2001]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertSame([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);
    }

    /* - season 1 with competitions => only season 1 and its competitions */
    public function testGettingOneSeasonWithCompetitionsAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        /** @var Competition $competition2_season1 */
        $competition2_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId() . '?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertArrayContent([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);
        $this->assertArrayHasKey('competitions', $data);
        $season1Competitions = $data['competitions'];
        $this->assertCount(2, $season1Competitions);
        $this->assertArrayContentByKey('id', $competition1_season1->getId(), [
            'id' => $competition1_season1->getId(),
            'name' => $competition1_season1->getName(),
        ], $season1Competitions);
        $this->assertArrayContentByKey('id', $competition2_season1->getId(), [
            'id' => $competition2_season1->getId(),
            'name' => $competition2_season1->getName(),
        ], $season1Competitions);
    }

    /************************
     * Season Administrator *
     ************************/

    /* - all seasons => only season 1 */
    public function testGettingAllSeasonsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        factory(Season::class)->create(['year' => 2001]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/seasons')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);
    }

    /* - all seasons with competitions => only season 1 its competitions */
    public function testGettingAllSeasonsWithCompetitionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        /** @var Competition $competition2_season1 */
        $competition2_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $data = $this->keyArrayBy($data, 'id');

        $this->assertCount(1, $data);

        $season1Data = $data[$season1->getId()];
        $this->assertArrayContent([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $season1Data);
        $this->assertArrayHasKey('competitions', $season1Data);
        $season1Competitions = $season1Data['competitions'];
        $this->assertCount(2, $season1Competitions);
        $this->assertArrayContentByKey('id', $competition1_season1->getId(), [
            'id' => $competition1_season1->getId(),
            'name' => $competition1_season1->getName(),
        ], $season1Competitions);
        $this->assertArrayContentByKey('id', $competition2_season1->getId(), [
            'id' => $competition2_season1->getId(),
            'name' => $competition2_season1->getName(),
        ], $season1Competitions);
    }

    /*
     * - season 1 => only season 1
     * - season 2 => no data
     */
    public function testGettingOneSeasonAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertSame([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);

        $response = $this->getJson('/api/v1/seasons/' . $season2->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * - season 1 with competitions => only season 1 with its competitions
     * - season 2 with competitions => no data
     */
    public function testGettingOneSeasonWithCompetitionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        /** @var Competition $competition2_season1 */
        $competition2_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId() . '?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertArrayContent([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);
        $this->assertArrayHasKey('competitions', $data);
        $season1Competitions = $data['competitions'];
        $this->assertCount(2, $season1Competitions);
        $this->assertArrayContentByKey('id', $competition1_season1->getId(), [
            'id' => $competition1_season1->getId(),
            'name' => $competition1_season1->getName(),
        ], $season1Competitions);
        $this->assertArrayContentByKey('id', $competition2_season1->getId(), [
            'id' => $competition2_season1->getId(),
            'name' => $competition2_season1->getName(),
        ], $season1Competitions);

        $response = $this->getJson('/api/v1/seasons/' . $season2->getId() . '?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*****************************
     * Competition Administrator *
     *****************************/

    /* - all seasons => only season 1 */
    public function testGettingAllSeasonsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1_season1)));

        $response = $this->getJson('/api/v1/seasons')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);
    }

    /* - all seasons with competitions => only season 1 with competition 1 */
    public function testGettingAllSeasonsWithCompetitionsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        /** @var Competition $competition2_season1 */
        $competition2_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1_season1)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $data = $this->keyArrayBy($data, 'id');

        $this->assertCount(1, $data);

        $season1Data = $data[$season1->getId()];
        $this->assertArrayContent([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $season1Data);
        $this->assertArrayHasKey('competitions', $season1Data);
        $season1Competitions = $season1Data['competitions'];
        $this->assertCount(1, $season1Competitions);
        $this->assertArrayContentByKey('id', $competition1_season1->getId(), [
            'id' => $competition1_season1->getId(),
            'name' => $competition1_season1->getName(),
        ], $season1Competitions);
    }

    /*
     * - season 1 => only season 1
     * - season 2 => no data
     */
    public function testGettingOneSeasonAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1_season1)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertSame([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);

        $response = $this->getJson('/api/v1/seasons/' . $season2->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * - season 1 with competitions => only season 1 with competition 1
     * - season 2 with competitions => no data
     */
    public function testGettingOneSeasonWithCompetitionsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        /** @var Competition $competition2_season1 */
        $competition2_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1_season1)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId() . '?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertArrayContent([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);
        $this->assertArrayHasKey('competitions', $data);
        $season1Competitions = $data['competitions'];
        $this->assertCount(1, $season1Competitions);
        $this->assertArrayContentByKey('id', $competition1_season1->getId(), [
            'id' => $competition1_season1->getId(),
            'name' => $competition1_season1->getName(),
        ], $season1Competitions);

        $response = $this->getJson('/api/v1/seasons/' . $season2->getId() . '?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /**************************
     * Division Administrator *
     **************************/

    /* - all season => only season 1 */
    public function testGettingAllSeasonsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        /** @var Division $division1 */
        $division1 = factory(Division::class)->create(['competition_id' => $competition1_season1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/seasons')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);
    }

    /* - all seasons with competitions => only season 1 with competition 1 */
    public function testGettingAllSeasonsWithCompetitionsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        /** @var Division $division1 */
        $division1 = factory(Division::class)->create(['competition_id' => $competition1_season1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $data = $this->keyArrayBy($data, 'id');

        $this->assertCount(1, $data);

        $season1Data = $data[$season1->getId()];
        $this->assertArrayContent([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $season1Data);
        $this->assertArrayHasKey('competitions', $season1Data);
        $season1Competitions = $season1Data['competitions'];
        $this->assertCount(1, $season1Competitions);
        $this->assertArrayContentByKey('id', $competition1_season1->getId(), [
            'id' => $competition1_season1->getId(),
            'name' => $competition1_season1->getName(),
        ], $season1Competitions);
    }

    /*
     * - season 1 => only season 1
     * - season 2 => no data
     */
    public function testGettingOneSeasonAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        /** @var Division $division1 */
        $division1 = factory(Division::class)->create(['competition_id' => $competition1_season1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertSame([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);

        $response = $this->getJson('/api/v1/seasons/' . $season2->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * - season 1 with competitions => only season 1 with competition 1
     * - season 2 with competitions => no data
     */
    public function testGettingOneSeasonWithCompetitionsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);

        /** @var Competition $competition1_season1 */
        $competition1_season1 = factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        /** @var Division $division1 */
        $division1 = factory(Division::class)->create(['competition_id' => $competition1_season1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId() . '?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertArrayContent([
            'id' => $season1->getId(),
            'name' => '2000/01',
        ], $data);
        $this->assertArrayHasKey('competitions', $data);
        $season1Competitions = $data['competitions'];
        $this->assertCount(1, $season1Competitions);
        $this->assertArrayContentByKey('id', $competition1_season1->getId(), [
            'id' => $competition1_season1->getId(),
            'name' => $competition1_season1->getName(),
        ], $season1Competitions);

        $response = $this->getJson('/api/v1/seasons/' . $season2->getId() . '?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /******************
     * Club Secretary *
     ******************/

    /* - all seasons => no data */
    public function testGettingAllSeasonsAsClubSecretary(): void
    {
        factory(Season::class)->create(['year' => 2000]);
        factory(Season::class)->create(['year' => 2001]);

        $club = aClub()->build();

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/seasons')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - all seasons with competitions => no data */
    public function testGettingAllSeasonsWithCompetitionsAsClubSecretary(): void
    {
        $season1 = factory(Season::class)->create(['year' => 2000]);
        $season2 = factory(Season::class)->create(['year' => 2001]);

        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        $club = aClub()->build();

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - season 1 => no data */
    public function testGettingOneSeasonAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        factory(Season::class)->create(['year' => 2001]);

        $club = aClub()->build();

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - season 1 with competitions => no data */
    public function testGettingOneSeasonWithCompetitionsAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        factory(Season::class)->create(['year' => 2001]);

        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);

        $club = aClub()->build();

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId() . '?with[]=competitions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /******************
     * Team Secretary *
     ******************/

    /* - all seasons => no data */
    public function testGettingAllSeasonsAsTeamSecretary(): void
    {
        factory(Season::class)->create(['year' => 2000]);
        factory(Season::class)->create(['year' => 2001]);

        $team = aTeam()->build();

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/seasons')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - all seasons with competitions => no data */
    public function testGettingAllSeasonsWithCompetitionsAsTeamSecretary(): void
    {
        $season1 = factory(Season::class)->create(['year' => 2000]);
        $season2 = factory(Season::class)->create(['year' => 2001]);

        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);
        factory(Competition::class)->create(['season_id' => $season2->getId()]);

        $team = aTeam()->build();

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/seasons?with[]=competitions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - season 1 => no data */
    public function testGettingOneSeasonAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        factory(Season::class)->create(['year' => 2001]);

        $team = aTeam()->build();

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - season 1 => no data */
    public function testGettingOneSeasonWithCompetitionsAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        factory(Season::class)->create(['year' => 2001]);

        factory(Competition::class)->create(['season_id' => $season1->getId()]);
        factory(Competition::class)->create(['season_id' => $season1->getId()]);

        $team = aTeam()->build();

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/seasons/' . $season1->getId() . '?with[]=competitions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }
}
