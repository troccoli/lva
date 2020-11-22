<?php

namespace Tests\Integration\Api\V1;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
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
        $this->assertEmpty($response->decodeResponseJson('data'));

        /** @var Season $season */
        $season = factory(Season::class)->create(['year' => 2000]);
        $response = $this->getJson('/api/v1/competitions?season=' . $season->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
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
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(3, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
        ], $data);
        $this->assertContains([
            'id' => $competition2->getId(),
            'name' => 'University Games',
        ], $data);
        $this->assertContains([
            'id' => $competition3->getId(),
            'name' => 'Minor Leagues',
        ], $data);
    }

    /* all competitions with seasons => all competitions and their seasons */
    public function testGettingAllCompetitionsWithSeasonsAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?with[]=season')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);
        $this->assertContains([
            'id' => $competition2->getId(),
            'name' => 'University Games',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);
    }

    /* all competitions with divisions => all competitions with their divisions */
    public function testGettingAllCompetitionsWithDivisionsAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(3, $data);

        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);
        $this->assertArrayContentByKey('id', $competition2->getId(), [
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
        ], $data);
        $this->assertArrayContentByKey('id', $competition3->getId(), [
            'id' => $competition3->getId(),
            'name' => 'Youth Games',
            'divisions' => [],
        ], $data);
    }

    /* all competitions with seasons and divisions => all competitions with their divisions, and their seasons */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(3, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);
        $this->assertArrayContentByKey('id', $competition2->getId(), [
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
        ], $data);
        $this->assertArrayContentByKey('id', $competition3->getId(), [
            'id' => $competition3->getId(),
            'name' => 'Youth Games',
            'season' => [
                'id' => $season2->getId(),
                'name' => '2001/02',
            ],
            'divisions' => [],
        ], $data);
    }

    /* all competitions for season 1 => all competitions in season 1 */
    public function testGettingAllCompetitionsForOneSeasonAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
        ], $data);
        $this->assertContains([
            'id' => $competition2->getId(),
            'name' => 'University Games',
        ], $data);
    }

    /* all competitions for season 1 with seasons => all competitions in season 1, and season 1 */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . "&with[]=season")
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);
        $this->assertContains([
            'id' => $competition2->getId(),
            'name' => 'University Games',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);
    }

    /* all competitions for season 1 with divisions => all competitions in season 1 with their divisions */
    public function testGettingAllCompetitionsForOneSeasonWithDivisionsAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);
        factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . "&with[]=divisions")
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);
    }

    /* all competitions for season 1 with seasons and divisions => all competitions in season 1 with their divisions, and season 1 */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAndDivisionsAsSiteAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);
        factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . "&with[]=season&with[]=divisions")
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);
    }

    /* competition 1 => only competition 1 */
    public function testGettingOneCompetitionAsSiteAdministrator(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season->getId(),
        ]);
        factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId())
            ->assertOk();

        $this->assertEquals([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
        ], $response->decodeResponseJson('data'));
    }

    /* competition 1 with season => only competition 1, and season 1 */
    public function testGettingOneCompetitionsWithSeasonsAsSiteAdministrator(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season->getId(),
        ]);
        factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . "?with[]=season")
            ->assertOk();

        $this->assertEquals([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
            'season' => [
                'id' => $season->getId(),
                'name' => '2000/01',
            ],
        ], $response->decodeResponseJson('data'));
    }

    /* competition 1 with divisions => only competition 1 with its divisions */
    public function testGettingOneCompetitionsWithDivisionsAsSiteAdministrator(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . "?with[]=divisions")
            ->assertOk();

        $this->assertEquals([
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
        ], $response->decodeResponseJson('data'));
    }

    /* competition 1 with seasons and divisions => only competition 1 with its divisions, and season 1 */
    public function testGettingOneCompetitionsWithSeasonsAndDivisionsAsSiteAdministrator(): void
    {
        /** @var Season $season */
        $season = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season->getId(),
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . "?with[]=season&with[]=divisions")
            ->assertOk();

        $this->assertEquals([
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
        ], $response->decodeResponseJson('data'));
    }

    /************************
     * Season Administrator *
     ************************/

    /* - all competitions => all competitions in season 1 */
    public function testGettingAllCompetitionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
        ], $data);
        $this->assertContains([
            'id' => $competition2->getId(),
            'name' => 'University Games',
        ], $data);
    }

    /* - all competitions with seasons => all competitions in season 1, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);
        $this->assertContains([
            'id' => $competition2->getId(),
            'name' => 'University Games',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);
    }

    /* - all competitions with divisions => all competitions in season 1 with their divisions */
    public function testGettingAllCompetitionsWithDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);

        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);
        $this->assertArrayContentByKey('id', $competition2->getId(), [
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
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);

        $this->assertArrayContentByKey('id', $competition3->getId(), [
            'id' => $competition3->getId(),
            'name' => 'Youth Games',
            'divisions' => [],
        ], $data);
    }

    /* - all competitions with seasons and divisions => all competitions in season 1 with their divisions, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);
        $this->assertArrayContentByKey('id', $competition2->getId(), [
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
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);

        $this->assertArrayContentByKey('id', $competition3->getId(), [
            'id' => $competition3->getId(),
            'name' => 'Youth Games',
            'season' => [
                'id' => $season2->getId(),
                'name' => '2001/02',
            ],
            'divisions' => [],
        ], $data);
    }

    /*
     * - all competitions for season 1 => all competitions in season 1
     * - all competitions for season 2 => no data
    */
    public function testGettingAllCompetitionsForOneSeasonAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
        ], $data);
        $this->assertContains([
            'id' => $competition2->getId(),
            'name' => 'University Games',
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - all competitions for season 1 with seasons => all competitions in season 1, and season 1
     * - all competitions for season 2 with seasons => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . "&with[]=season")
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);
        $this->assertContains([
            'id' => $competition2->getId(),
            'name' => 'University Games',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId() . "&with[]=season")
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - all competitions for season 1 with divisions => all competitions in season 1 with their divisions
     * - all competitions for season 2 with divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);
        factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . "&with[]=divisions")
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId() . "&with[]=divisions")
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - all competitions for season 1 with seasons and divisions => all competitions in season 1 with their divisions, and season 1
     * - all competitions for season 2 with seasons and divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAndDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);
        factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . "&with[]=season&with[]=divisions")
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId() . "&with[]=season&with[]=divisions")
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 => only competitions 1
     * - competition 2 => no data
     */
    public function testGettingOneCompetitionAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId())
            ->assertOk();

        $this->assertEquals([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition3->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 with seasons => only competition 1, and season 1
     * - competition 2 with seasons => no data
     */
    public function testGettingOneCompetitionWithSeasonsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=season')
            ->assertOk();

        $this->assertEquals([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId() . '?with[]season')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 with divisions => only competition 1 with its divisions
     * - competition 2 with divisions => no data
     */
    public function testGettingOneCompetitionWithDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . "?with[]=divisions")
            ->assertOk();

        $this->assertEquals([
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
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId() . "?with[]=divisions")
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 with seasons and divisions => only competition 1 with its divisions, and season 1
     * - competition 2 with seasons and divisions => no data
     */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . "?with[]=season&with[]=divisions")
            ->assertOk();

        $this->assertEquals([
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
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId() . "?with[]=season&with[]=divisions")
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*****************************
     * Competition Administrator *
     *****************************/

    /* - all competitions => only competition 1 */
    public function testGettingAllCompetitionsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
        ], $data);
    }

    /* - all competitions with seasons => only competition 1, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);
    }

    /* - all competitions with divisions => only competitions 1 with its divisions */
    public function testGettingAllCompetitionsWithDivisionsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);
    }

    /* - all competitions with seasons and divisions => only competition 1 with its divisions, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);
    }

    /*
     * - all competitions for season 1 => only competition 1
     * - all competitions for season 2 => no data
     */
    public function testGettingAllCompetitionsForOneSeasonAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - all competitions for season 1 with seasons => only competition 1, and season 1
     * - all competitions for season 2 with seasons => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . '&with[]=season')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId() . '&with[]=season')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - all competitions for season 1 with divisions => only competitions 1 with its divisions
     * - all competitions for season 2 with divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithDivisionsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);
        factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . "&with[]=divisions")
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId() . "&with[]=divisions")
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - all competitions for season 1 with seasons and divisions => only competition 1 with its divisions, and season 1
     * - all competitions for season 2 with seasons and divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAndDivisionsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);
        factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . "&with[]=season&with[]=divisions")
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertArrayContentByKey('id', $competition1->getId(), [
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
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId() . "&with[]=season&with[]=divisions")
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 => only competition 1
     * - competition 2 => no data
     */
    public function testGettingOneCompetitionAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId())
            ->assertOk();

        $this->assertEquals([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition3->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 with seasons => only competition 1, and season 1
     * - competition 2 with seasons => no data
     */
    public function testGettingOneCompetitionWithSeasonsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=season')
            ->assertOk();

        $this->assertEquals([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId() . '?with[]=season')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition3->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 with divisions => only competition 1 with its divisions
     * - competition 2 with divisions => no data
     */
    public function testGettingOneCompetitionWithDivisionsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=divisions')
            ->assertOk();

        $this->assertEquals([
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
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId() . '?with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 with seasons and divisions => only competition 1 with its divisions, and season 1
     * - competition 2 with seasons and divisions => no data
     */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsCompetitionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=season&with[]=divisions')
            ->assertOk();

        $this->assertEquals([
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
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId() . '?with[]=season&with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /**************************
     * Division Administrator *
     **************************/

    /* - all competitions => only competition 1 */
    public function testGettingAllCompetitionsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
        ], $data);
    }

    /* - all competitions with seasons => only competition 1, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);
    }

    /* - all competitions with divisions => only competitions 1 with division 1 */
    public function testGettingAllCompetitionsWithDivisionsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
            'divisions' => [
                [
                    'id' => $division1->getId(),
                    'name' => 'MP',
                    'display_order' => 1,
                ],
            ],
        ], $data);
    }

    /* - all competitions with seasons and divisions => only competition 1 with division 1, and season 1 */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
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
        ], $data);
    }

    /*
     * - all competitions for season 1 => only competition 1
     * - all competitions for season 2 => no data
     */
    public function testGettingAllCompetitionsForOneSeasonAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - all competitions for season 1 with seasons => only competition 1, and season 1
     * - all competitions for season 2 with seasons => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . '&with[]=season')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId() . '&with[]=season')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - all competitions for season 1 with divisions => only competitions 1 with division 1
     * - all competitions for season 2 with divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithDivisionsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . '&with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
            'divisions' => [
                [
                    'id' => $division1->getId(),
                    'name' => 'MP',
                    'display_order' => 1,
                ],
            ],
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId() . '&with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - all competitions for season 1 with seasons and divisions => only competition 1 with division 1, and season 1
     * - all competitions for season 2 with seasons and divisions => no data
     */
    public function testGettingAllCompetitionsForOneSeasonWithSeasonsAndDivisionsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions?season=' . $season1->getId() . '&with[]=season&with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
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
        ], $data);

        $response = $this->getJson('/api/v1/competitions?season=' . $season2->getId() . '&with[]=season&with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 => only competition 1
     * - competition 2 => no data
     */
    public function testGettingOneCompetitionAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId())
            ->assertOk();

        $this->assertEquals([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 with seasons => only competition 1, and season 1
     * - competition 2 with seasons => no data
     */
    public function testGettingOneCompetitionWithSeasonsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=season')
            ->assertOk();

        $this->assertEquals([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
            'season' => [
                'id' => $season1->getId(),
                'name' => '2000/01',
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId() . '?with[]=season')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 with divisions => only competition 1 with division 1
     * - competition 2 with divisions => no data
     */
    public function testGettingOneCompetitionWithDivisionsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=divisions')
            ->assertOk();

        $this->assertEquals([
            'id' => $competition1->getId(),
            'name' => 'London Magic League',
            'divisions' => [
                [
                    'id' => $division1->getId(),
                    'name' => 'MP',
                    'display_order' => 1,
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId() . '?with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * - competition 1 with seasons and divisions => only competition 1 with division 1, and season 1
     * - competition 2 with seasons and divisions => no data
     */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsDivisionAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London Magic League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'university Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=season&with[]=divisions')
            ->assertOk();

        $this->assertEquals([
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
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/competitions/' . $competition2->getId() . '?with[]=season&with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /******************
     * Club Secretary *
     ******************/

    /* - all competitions => no data */
    public function testGettingAllCompetitionsAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        $club = aClub()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - all competitions with season => no data */
    public function testGettingAllCompetitionsWithSeasonsAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        $club = aClub()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - all competitions with divisions => no data */
    public function testGettingAllCompetitionsWithDivisionsAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $club = aClub()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - all competitions with season and divisions => no data */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $club = aClub()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - competition 1 => no data */
    public function testGettingOneCompetitionAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $club = aClub()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - competition 1 with season => no data */
    public function testGettingOneCompetitionWithSeasonsAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $club = aClub()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=season')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - competition 1 with divisions => no data */
    public function testGettingOneCompetitionWithDivisionsAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $club = aClub()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - competition 1 with season and divisions => no data */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsClubSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $club = aClub()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=season&with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /******************
     * Team Secretary *
     ******************/

    /* - all competitions => no data */
    public function testGettingAllCompetitionsAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        $team = aTeam()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - all competitions with season => no data */
    public function testGettingAllCompetitionsWithSeasonsAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        $team = aTeam()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions?with[]=season')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - all competitions with divisions => no data */
    public function testGettingAllCompetitionsWithDivisionsAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $team = aTeam()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions?with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - all competitions with season and divisions => no data */
    public function testGettingAllCompetitionsWithSeasonsAndDivisionsAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $team = aTeam()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions?with[]=season&with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - competition 1 => no data */
    public function testGettingOneCompetitionAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $team = aTeam()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId())
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - competition 1 with season => no data */
    public function testGettingOneCompetitionWithSeasonsAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $team = aTeam()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=season')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - competition 1 with divisions => no data */
    public function testGettingOneCompetitionWithDivisionsAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $team = aTeam()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* - competition 1 with season and divisions => no data */
    public function testGettingOneCompetitionWithSeasonsAndDivisionsAsTeamSecretary(): void
    {
        /** @var Season $season1 */
        $season1 = factory(Season::class)->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = factory(Competition::class)->create([
            'name' => 'London League',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division1 */
        $division1 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order' => 1,
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);

        $team = aTeam()->build();
        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team)));

        $response = $this->getJson('/api/v1/competitions/' . $competition1->getId() . '?with[]=season&with[]=divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }
}
