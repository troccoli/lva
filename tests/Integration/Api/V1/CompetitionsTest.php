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

class CompetitionsTest extends TestCase
{
    use InteractsWithArrays;

    public function testGettingAllCompetitionsWhenThereAreNone(): void
    {
        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $season = Season::factory()->create(['year' => 2000]);
        $response = $this->getJson("/api/v1/competitions?season={$season->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    public function testGettingAllCompetitionsForNonExistingSeason(): void
    {
        Passport::actingAs($this->siteAdmin);

        $this->getJson('/api/v1/competitions?season=1')
             ->assertNotFound();
    }

    public function testGettingANonExistingCompetition(): void
    {
        Passport::actingAs($this->siteAdmin);

        $this->getJson('/api/v1/competitions/1')
             ->assertNotFound();
    }

    /**********************
     * Site Administrator *
     **********************/

    /* all competitions => all competitions */
    public function testGettingAllCompetitionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);

        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(3, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            $competitions
        );
        $this->assertContains(
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            $competitions
        );
        $this->assertContains(
            [
                'id' => $competition3->getId(),
                'name' => 'Minor Leagues',
            ],
            $competitions
        );
    }

    /* all competitions with seasons => all competitions and their seasons */
    public function testGettingAllCompetitionsWithSeasonsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?with[]=season')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(2, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
        $this->assertContains(
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
    }

    /* all competitions with divisions => all competitions with their divisions */
    public function testGettingAllCompetitionsWithDivisionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(3, $competitions);

        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition2->getId(),
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
                'divisions' => [
                    [
                        'id' => $division3->getId(),
                        'name' => 'MP',
                        'display_order' => 10,
                    ],
                    [
                        'id' => $division4->getId(),
                        'name' => 'WP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition3->getId(),
            [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
                'divisions' => [],
            ],
            $competitions
        );
    }

    /* all competitions with seasons and divisions => all competitions with their divisions, and their seasons */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');

        $this->assertCount(3, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition2->getId(),
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division3->getId(),
                        'name' => 'MP',
                        'display_order' => 10,
                    ],
                    [
                        'id' => $division4->getId(),
                        'name' => 'WP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition3->getId(),
            [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
                'season' => [
                    'id' => $season2->getId(),
                    'name' => '2001/02',
                ],
                'divisions' => [],
            ],
            $competitions
        );
    }

    /* all competitions for season 1 => all competitions in season 1 */
    public function testGettingAllCompetitionsForOneSeasonAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?season='.$season1->getId())
                         ->assertOk();

        $competitions = $response->json('data');

        $this->assertCount(2, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            $competitions
        );
        $this->assertContains(
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            $competitions
        );
    }

    /* all competitions for season 1 with seasons => all competitions in season 1, and season 1 */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=season")
                         ->assertOk();

        $competitions = $response->json('data');

        $this->assertCount(2, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
        $this->assertContains(
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
    }

    /* all competitions for season 1 with divisions => all competitions in season 1 with their divisions */
    public function testGettingAllCompetitionsForOneSeasonWithDivisionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');

        $this->assertCount(1, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
    }

    /* all competitions for season 1 with seasons and divisions => all competitions in season 1 with their divisions, and season 1 */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAndDivisionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=season&with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');

        $this->assertCount(1, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
    }

    /* competition 1 => only competition 1 */
    public function testGettingOneCompetitionAsSiteAdministrator(): void
    {
        $season = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season)->create(['name' => 'London Magic League']);
        Competition::factory()->for($season)->create(['name' => 'university Games']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}")
                         ->assertOk();

        $competitions = $response->json('data');

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
            ],
            $competitions
        );
    }

    /* competition 1 with season => only competition 1, and season 1 */
    public function testGettingOneCompetitionsWithSeasonsAsSiteAdministrator(): void
    {
        $season = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season)->create(['name' => 'London Magic League']);
        Competition::factory()->for($season)->create(['name' => 'university Games']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season")
                         ->assertOk();

        $competitions = $response->json('data');

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
    }

    /* competition 1 with divisions => only competition 1 with its divisions */
    public function testGettingOneCompetitionsWithDivisionsAsSiteAdministrator(): void
    {
        $season = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season)->create(['name' => 'London Magic League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        Competition::factory()->for($season)->create(['name' => 'university Games']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
    }

    /* competition 1 with seasons and divisions => only competition 1 with its divisions, and season 1 */
    public function testGettingOneCompetitionsWithSeasonsAndDivisionsAsSiteAdministrator(): void
    {
        $season = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season)->create(['name' => 'London Magic League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        Competition::factory()->for($season)->create(['name' => 'university Games']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season&with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
    }

    /************************
     * Season Administrator *
     ************************/

    /* - all competitions => all competitions in season 1 */
    public function testGettingAllCompetitionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(2, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            $competitions
        );
        $this->assertContains(
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            $competitions
        );
    }

    /* - all competitions with seasons => all competitions in season 1, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(2, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
        $this->assertContains(
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
    }

    /* - all competitions with divisions => all competitions in season 1 with their divisions */
    public function testGettingAllCompetitionsWithDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        /** @var Season $season2 */
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(2, $competitions);

        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition2->getId(),
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
                'divisions' => [
                    [
                        'id' => $division3->getId(),
                        'name' => 'MP',
                        'display_order' => 10,
                    ],
                    [
                        'id' => $division4->getId(),
                        'name' => 'WP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $competitions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);

        $this->assertArrayContentByKey(
            'id',
            $competition3->getId(),
            [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
                'divisions' => [],
            ],
            $competitions
        );
    }

    /* - all competitions with seasons and divisions => all competitions in season 1 with their divisions, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        /** @var Season $season2 */
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(2, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
        $this->assertArrayContentByKey(
            'id',
            $competition2->getId(),
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division3->getId(),
                        'name' => 'MP',
                        'display_order' => 10,
                    ],
                    [
                        'id' => $division4->getId(),
                        'name' => 'WP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $competitions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);

        $this->assertArrayContentByKey(
            'id',
            $competition3->getId(),
            [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
                'season' => [
                    'id' => $season2->getId(),
                    'name' => '2001/02',
                ],
                'divisions' => [],
            ],
            $competitions
        );
    }

    /*
     * - all competitions for season 1 => all competitions in season 1
     * - all competitions for season 2 => no data
    */
    public function testGettingAllCompetitionsForOneSeasonAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(2, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            $competitions
        );
        $this->assertContains(
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - all competitions for season 1 with seasons => all competitions in season 1, and season 1
     * - all competitions for season 2 with seasons => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=season")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(2, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
        $this->assertContains(
            [
                'id' => $competition2->getId(),
                'name' => 'University Games',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}&with[]=season")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - all competitions for season 1 with divisions => all competitions in season 1 with their divisions
     * - all competitions for season 2 with divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - all competitions for season 1 with seasons and divisions => all competitions in season 1 with their divisions, and season 1
     * - all competitions for season 2 with seasons and divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAndDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=season&with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}&with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 => only competitions 1
     * - competition 2 => no data
     */
    public function testGettingOneCompetitionAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        Competition::factory()->for($season1)->create(['name' => 'Youth Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'University Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition3->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 with seasons => only competition 1, and season 1
     * - competition 2 with seasons => no data
     */
    public function testGettingOneCompetitionWithSeasonsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}?with[]season")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 with divisions => only competition 1 with its divisions
     * - competition 2 with divisions => no data
     */
    public function testGettingOneCompetitionWithDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=divisions")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}?with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 with seasons and divisions => only competition 1 with its divisions, and season 1
     * - competition 2 with seasons and divisions => no data
     */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}?with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*****************************
     * Competition Administrator *
     *****************************/

    /* - all competitions => only competition 1 */
    public function testGettingAllCompetitionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            $competitions
        );
    }

    /* - all competitions with seasons => only competition 1, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
    }

    /* - all competitions with divisions => only competitions 1 with its divisions */
    public function testGettingAllCompetitionsWithDivisionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
    }

    /* - all competitions with seasons and divisions => only competition 1 with its divisions, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );
    }

    /*
     * - all competitions for season 1 => only competition 1
     * - all competitions for season 2 => no data
     */
    public function testGettingAllCompetitionsForOneSeasonAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - all competitions for season 1 with seasons => only competition 1, and season 1
     * - all competitions for season 2 with seasons => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=season")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}&with[]=season")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - all competitions for season 1 with divisions => only competitions 1 with its divisions
     * - all competitions for season 2 with divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithDivisionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - all competitions for season 1 with seasons and divisions => only competition 1 with its divisions, and season 1
     * - all competitions for season 2 with seasons and divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAndDivisionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=season&with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertArrayContentByKey(
            'id',
            $competition1->getId(),
            [
                'id' => $competition1->getId(),
                'name' => 'London League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}&with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 => only competition 1
     * - competition 2 => no data
     */
    public function testGettingOneCompetitionAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'Youth Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'University Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/competitions/{$competition3->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 with seasons => only competition 1, and season 1
     * - competition 2 with seasons => no data
     */
    public function testGettingOneCompetitionWithSeasonsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'Youth Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'University Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}?with[]=season")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/competitions/{$competition3->getId()}?with[]=season")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 with divisions => only competition 1 with its divisions
     * - competition 2 with divisions => no data
     */
    public function testGettingOneCompetitionWithDivisionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=divisions")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}?with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 with seasons and divisions => only competition 1 with its divisions, and season 1
     * - competition 2 with seasons and divisions => no data
     */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                    [
                        'id' => $division2->getId(),
                        'name' => 'WP',
                        'display_order' => 10,
                    ],
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}?with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /**************************
     * Division Administrator *
     **************************/

    /* - all competitions => only competition 1 */
    public function testGettingAllCompetitionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
            ],
            $competitions
        );
    }

    /* - all competitions with seasons => only competition 1, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );
    }

    /* - all competitions with divisions => only competitions 1 with division 1 */
    public function testGettingAllCompetitionsWithDivisionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $competitions
        );
    }

    /* - all competitions with seasons and divisions => only competition 1 with division 1, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $competitions
        );
    }

    /*
     * - all competitions for season 1 => only competition 1
     * - all competitions for season 2 => no data
     */
    public function testGettingAllCompetitionsForOneSeasonAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - all competitions for season 1 with seasons => only competition 1, and season 1
     * - all competitions for season 2 with seasons => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=season")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}&with[]=season")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - all competitions for season 1 with divisions => only competitions 1 with division 1
     * - all competitions for season 2 with divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithDivisionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - all competitions for season 1 with seasons and divisions => only competition 1 with division 1, and season 1
     * - all competitions for season 2 with seasons and divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAndDivisionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/competitions?season={$season1->getId()}&with[]=season&with[]=divisions")
                         ->assertOk();

        $competitions = $response->json('data');
        $this->assertCount(1, $competitions);
        $this->assertContains(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $competitions
        );

        $response = $this->getJson("/api/v1/competitions?season={$season2->getId()}&with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 => only competition 1
     * - competition 2 => no data
     */
    public function testGettingOneCompetitionAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 with seasons => only competition 1, and season 1
     * - competition 2 with seasons => no data
     */
    public function testGettingOneCompetitionWithSeasonsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}?with[]=season")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 with divisions => only competition 1 with division 1
     * - competition 2 with divisions => no data
     */
    public function testGettingOneCompetitionWithDivisionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=divisions")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}?with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /*
     * - competition 1 with seasons and divisions => only competition 1 with division 1, and season 1
     * - competition 2 with seasons and divisions => no data
     */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London Magic League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition2 = Competition::factory()->for($season2)->create(['name' => 'university Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEquals(
            [
                'id' => $competition1->getId(),
                'name' => 'London Magic League',
                'season' => [
                    'id' => $season1->getId(),
                    'name' => '2000/01',
                ],
                'divisions' => [
                    [
                        'id' => $division1->getId(),
                        'name' => 'MP',
                        'display_order' => 1,
                    ],
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/competitions/{$competition2->getId()}?with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /******************
     * Club Secretary *
     ******************/

    /* - all competitions => no data */
    public function testGettingAllCompetitionsAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Competition::factory()->for($season1)->create(['name' => 'London League']);
        Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        /** @var Club $club */
        $club = Club::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - all competitions with season => no data */
    public function testGettingAllCompetitionsWithSeasonsAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Competition::factory()->for($season1)->create(['name' => 'London League']);
        Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        /** @var Club $club */
        $club = Club::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - all competitions with divisions => no data */
    public function testGettingAllCompetitionsWithDivisionsAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Club $club */
        $club = Club::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - all competitions with season and divisions => no data */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Club $club */
        $club = Club::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - competition 1 => no data */
    public function testGettingOneCompetitionAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Club $club */
        $club = Club::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - competition 1 with season => no data */
    public function testGettingOneCompetitionWithSeasonsAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Club $club */
        $club = Club::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - competition 1 with divisions => no data */
    public function testGettingOneCompetitionWithDivisionsAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Club $club */
        $club = Club::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - competition 1 with season and divisions => no data */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsClubSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Club $club */
        $club = Club::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /******************
     * Team Secretary *
     ******************/

    /* - all competitions => no data */
    public function testGettingAllCompetitionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Competition::factory()->for($season1)->create(['name' => 'London League']);
        Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        /** @var Team $team */
        $team = Team::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - all competitions with season => no data */
    public function testGettingAllCompetitionsWithSeasonsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        Competition::factory()->for($season1)->create(['name' => 'London League']);
        Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Minor Leagues']);

        /** @var Team $team */
        $team = Team::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - all competitions with divisions => no data */
    public function testGettingAllCompetitionsWithDivisionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Team $team */
        $team = Team::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - all competitions with season and divisions => no data */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Team $team */
        $team = Team::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - competition 1 => no data */
    public function testGettingOneCompetitionAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Team $team */
        $team = Team::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - competition 1 with season => no data */
    public function testGettingOneCompetitionWithSeasonsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Team $team */
        $team = Team::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - competition 1 with divisions => no data */
    public function testGettingOneCompetitionWithDivisionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Team $team */
        $team = Team::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    /* - competition 1 with season and divisions => no data */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        Competition::factory()->for($season2)->create(['name' => 'Youth Games']);

        /** @var Team $team */
        $team = Team::factory()->create();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson("/api/v1/competitions/{$competition1->getId()}?with[]=season&with[]=divisions")
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }
}
