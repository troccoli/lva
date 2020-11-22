<?php

namespace Tests\Integration\Api\V1;

use App\Helpers\RolesHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use Laravel\Passport\Passport;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class DivisionsTest extends TestCase
{
    use InteractsWithArrays;

    public function testGettingAllDivisionsWhenThereAreNone(): void
    {
        Passport::actingAs($this->siteAdmin);

        $response = $this->get('/api/v1/divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    public function testGettingAllDivisionsForNonExistingCompetition(): void
    {
        Passport::actingAs($this->siteAdmin);

        $this->getJson('/api/v1/divisions?competition=1')
            ->assertNotFound();
    }

    public function testGettingANonExistingDivision(): void
    {
        Passport::actingAs($this->siteAdmin);

        $this->get('/api/v1/divisions/1')
            ->assertNotFound();
    }


    /**********************
     * Site Administrator *
     **********************/

    /* all divisions => all divisions */
    public function testGettingAllDivisionsAsSiteAdministrator(): void
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(5, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
        ], $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
        ], $data);
        $this->assertContains([
            'id' => $division5->getId(),
            'name' => 'WP',
            'display_order' => 1,
        ], $data);
    }

    /* all divisions with competitions => all divisions and their competition */
    public function testGettingAllDivisionsWithCompetitionsAsSiteAdministrator(): void
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(5, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division5->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
        ], $data);
    }

    /* all divisions with teams => all divisions and their teams */
    public function testGettingAllDivisionsWithTeamsAsSiteAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(3, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'teams' => [],
        ], $data);
    }

    /* all divisions with competitions and teams => all divisions and their competition and teams */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsSiteAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(3, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
            'teams' => [],
        ], $data);
    }

    /* all divisions in competition 1 => all divisions in competition 1 */
    public function testGettingAllDivisionsForOneCompetitionAsSiteAdministrator(): void
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

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
        ], $data);
    }

    /* all divisions in competition 1 with competitions => all divisions in competitions 1 and their competition 1 */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsSiteAdministrator(): void
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

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
    }

    /* all divisions in competition 1 with teams => all divisions in competition 1 and their teams */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsSiteAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
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

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);
    }

    /* all divisions in competition 1 with competitions and teams =>  all divisions in competitions 1 and their competition 1 and teams */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsSiteAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
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

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);
    }

    /* division 1 => division 1 */
    public function testGettingOneDivisionAsSiteAdministrator(): void
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
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId())
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId())
            ->assertOk();
        $this->assertEquals([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId())
            ->assertOk();
        $this->assertEquals([
            'id' => $division3->getId(),
            'name' => 'WP',
            'display_order' => 1,
        ], $response->decodeResponseJson('data'));
    }

    /* division 1 with competitions => division 1 and its competition */
    public function testGettingOneDivisionWithCompetitionsAsSiteAdministrator(): void
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
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEquals([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEquals([
            'id' => $division3->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
        ], $response->decodeResponseJson('data'));
    }

    /* division 1 with teams => division 1 and its teams */
    public function testGettingOneDivisionWithTeamsAsSiteAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team2 = aTeam()->withName('The Bears')->inDivision($division2)->build();
        $team3 = aTeam()->withName('The Dolphins')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('The Bats')->inDivision($division3)->build();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'teams' => [
                [
                    'id' => $team2->getId(),
                    'name' => 'The Bears',
                ],
                [
                    'id' => $team3->getId(),
                    'name' => 'The Dolphins',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division3->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team4->getId(),
                    'name' => 'The Bats',
                ],
            ],
        ], $response->decodeResponseJson('data'));
    }

    /* division 1 with teams and competitions => division 1 and its competition and teams */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsSiteAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team2 = aTeam()->withName('The Bears')->inDivision($division2)->build();
        $team3 = aTeam()->withName('The Dolphins')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('The Bats')->inDivision($division3)->build();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            'teams' => [
                [
                    'id' => $team2->getId(),
                    'name' => 'The Bears',
                ],
                [
                    'id' => $team3->getId(),
                    'name' => 'The Dolphins',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division3->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
            'teams' => [
                [
                    'id' => $team4->getId(),
                    'name' => 'The Bats',
                ],
            ],
        ], $response->decodeResponseJson('data'));
    }

    /************************
     * Season Administrator *
     ************************/

    /* all divisions => all divisions in season 1 */
    public function testGettingAllDivisionsAsSeasonAdministrator(): void
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(4, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
        ], $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division5->getId(),
            'name' => 'WP',
            'display_order' => 1,
        ], $data);
    }

    /* all divisions with competitions => all divisions in season 1 and their competition */
    public function testGettingAllDivisionsWithCompetitionsAsSeasonAdministrator(): void
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(4, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division5->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
        ], $data);
    }

    /* all divisions with teams => all divisions in season 1 and their teams */
    public function testGettingAllDivisionsWithTeamsAsSeasonAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division3)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division3)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team4->getId(),
                    'name' => 'London Trotters',
                ],
                [
                    'id' => $team5->getId(),
                    'name' => 'Globe Trotters',
                ],
            ],
        ], $data);
    }

    /* all divisions with competitions and teams => all divisions in season 1 and their competition and teams */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsSeasonAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division3)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division3)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
            'teams' => [
                [
                    'id' => $team4->getId(),
                    'name' => 'London Trotters',
                ],
                [
                    'id' => $team5->getId(),
                    'name' => 'Globe Trotters',
                ],
            ],
        ], $data);
    }

    /*
     * all divisions in competition 1 => all divisions in competition 1
     * all divisions in competition 2 => no data
    */
    public function testGettingAllDivisionsForOneCompetitionAsSeasonAdministrator(): void
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * all divisions in competition 1 with competitions => all divisions in competitions 1 and their competition 1
     * all divisions in competition 2 with competitions => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsSeasonAdministrator(): void
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * all divisions in competition 1 with teams => all divisions in competition 1 and their teams
     * all divisions in competition 2 with teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsSeasonAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division3)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division3)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * all divisions in competition 1 with competitions and teams => all divisions in competitions 1 and their competition 1 and teams
     * all divisions in competition 2 with competitions and teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsSeasonAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division3)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division3)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * division 1 => division 1
     * division 2 => division no data
     */
    public function testGettingOneDivisionAsSeasonAdministrator(): void
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId())
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId())
            ->assertOk();
        $this->assertEquals([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division5->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * division 1 with competitions => division 1 and its competition
     * division 2 with competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAsSeasonAdministrator(): void
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEquals([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division5->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * division 1 with teams => division 1 and its teams
     * division 2 with teams => no data
     */
    public function testGettingOneDivisionWithTeamsAsSeasonAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
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
        $team2 = aTeam()->withName('The Bears')->inDivision($division3)->build();
        $team3 = aTeam()->withName('The Dolphins')->inDivision($division3)->build();
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('The Bats')->inDivision($division5)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'teams' => [
                [
                    'id' => $team2->getId(),
                    'name' => 'The Bears',
                ],
                [
                    'id' => $team3->getId(),
                    'name' => 'The Dolphins',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division5->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
    * division 1 with teams and competitions => division 1 and its competition and teams
    * division 2 with teams and competitions => no data
    */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsSeasonAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
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
        $team2 = aTeam()->withName('The Bears')->inDivision($division3)->build();
        $team3 = aTeam()->withName('The Dolphins')->inDivision($division3)->build();
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
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('The Bats')->inDivision($division5)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            'teams' => [
                [
                    'id' => $team2->getId(),
                    'name' => 'The Bears',
                ],
                [
                    'id' => $team3->getId(),
                    'name' => 'The Dolphins',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division5->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*****************************
     * Competition Administrator *
     *****************************/

    /* all divisions => all divisions in competition 1 */
    public function testGettingAllDivisionsAsCompetitionAdministrator(): void
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
        /** @var Competition $competition2 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition2)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition3)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division5->getId(),
            'name' => 'WP',
            'display_order' => 1,
        ], $data);
    }

    /* all divisions with competitions => all divisions in competition 1 and their competition */
    public function testGettingAllDivisionsWithCompetitionsAsCompetitionAdministrator(): void
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
        /** @var Competition $competition2 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division5->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
        ], $data);
    }

    /* all divisions with teams => all divisions in competition 1 and their teams */
    public function testGettingAllDivisionsWithTeamsAsCompetitionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
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
        /** @var Competition $competition2 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division5)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division5)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition2)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'teams' => [],
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'teams' => [],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition3)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division5->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team4->getId(),
                    'name' => 'London Trotters',
                ],
                [
                    'id' => $team5->getId(),
                    'name' => 'Globe Trotters',
                ],
            ],
        ], $data);
    }

    /* all divisions with competitions and teams => all divisions in competition 1 and their competition and teams */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsCompetitionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
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
        /** @var Competition $competition2 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division5 */
        $division5 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division5)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division5)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            'teams' => [],
        ], $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            'teams' => [],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division5->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
            'teams' => [
                [
                    'id' => $team4->getId(),
                    'name' => 'London Trotters',
                ],
                [
                    'id' => $team5->getId(),
                    'name' => 'Globe Trotters',
                ],
            ],
        ], $data);
    }

    /*
     * all divisions in competition 1 => all divisions in competition 1
     * all divisions in competition 2 => no data
     */
    public function testGettingAllDivisionsForOneCompetitionAsCompetitionAdministrator(): void
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

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * all divisions in competition 1 with competitions => all divisions in competitions 1 and their competition 1
     * all divisions in competition 2 with competitions => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsCompetitionAdministrator(): void
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

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * all divisions in competition 1 with teams => all divisions in competition 1 and their teams
     * all divisions in competition 2 with teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsCompetitionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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
        $team4 = aTeam()->withName('London Trotters')->inDivision($division3)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division3)->build();
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'teams' => [],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * all divisions in competition 1 with competitions and teams => all divisions in competitions 1 and their competition 1 and teams
     * all divisions in competition 2 with competitions and teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsCompetitionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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
        $team4 = aTeam()->withName('London Trotters')->inDivision($division3)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division3)->build();
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * division 1 => division 1
     * division 2 => division no data
     */
    public function testGettingOneDivisionAsCompetitionAdministrator(): void
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

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId())
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId())
            ->assertOk();
        $this->assertEquals([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * division 1 with competitions => division 1 and its competition
     * division 2 with competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAsCompetitionAdministrator(): void
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

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEquals([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * division 1 with teams => division 1 and its teams
     * division 2 with teams => no data
     */
    public function testGettingOneDivisionWithTeamsAsCompetitionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'teams' => [],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /*
     * division 1 with teams and competitions => division 1 and its competition and teams
     * division 2 with teams and competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsCompetitionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEquals([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [],
        ], $response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /**************************
     * Division Administrator *
     **************************/

    /* all divisions => only division 1 */
    public function testGettingAllDivisionsAsDivisionAdministrator(): void
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
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division2)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division3)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division4)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
        ], $data);
    }

    /* all divisions with competitions => only division 1 and its competition */
    public function testGettingAllDivisionsWithCompetitionsAsDivisionAdministrator(): void
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
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division4)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
        ], $data);
    }

    /* all divisions with teams => only division 1 and its teams */
    public function testGettingAllDivisionsWithTeamsAsDivisionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division3)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division4)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division4)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division2)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'teams' => [],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division3)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division4)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team4->getId(),
                    'name' => 'London Trotters',
                ],
                [
                    'id' => $team5->getId(),
                    'name' => 'Globe Trotters',
                ],
            ],
        ], $data);
    }

    /* all divisions with competitions and teams => only division 1 and its competition and teams */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsDivisionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division3)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division4)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division4)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division2->getId(),
            'name' => 'WP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division3->getId(),
            'name' => 'MP',
            'display_order' => 10,
            'competition' => [
                'id' => $competition2->getId(),
                'name' => 'University Games',
            ],
            'teams' => [
                [
                    'id' => $team3->getId(),
                    'name' => 'Hot Stuff',
                ],
            ],
        ], $data);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division4)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division4->getId(),
            'name' => 'WP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition3->getId(),
                'name' => 'Youth Games',
            ],
            'teams' => [
                [
                    'id' => $team4->getId(),
                    'name' => 'London Trotters',
                ],
                [
                    'id' => $team5->getId(),
                    'name' => 'Globe Trotters',
                ],
            ],
        ], $data);
    }

    /*
     * all divisions in competition 1 => only division 1
     * all divisions in competition 2 => no data
     */
    public function testGettingAllDivisionsForOneCompetitionAsDivisionAdministrator(): void
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
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * all divisions in competition 1 with competitions => only division 1 and its competition
     * all divisions in competition 2 with competitions => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsDivisionAdministrator(): void
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
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * all divisions in competition 1 with teams => only division 1 and its teams
     * all divisions in competition 2 with teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsDivisionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division3)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division4)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division4)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * all divisions in competition 1 with competitions and teams => only division 1 and its competition and teams
     * all divisions in competition 2 with competitions and teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsDivisionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division3)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division4)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division4)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertCount(1, $data);
        $this->assertContains([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * division 1 => division 1
     * division 2 => no data
     */
    public function testGettingOneDivisionAsDivisionAdministrator(): void
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
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertSame([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
        ], $data);

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions/' . $division4->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * division 1 with competitions => division 1 and its competition
     * division 2 with competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAsDivisionAdministrator(): void
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
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertSame([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions/' . $division4->getId() . '?with[]=competition')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * division 1 with teams => division 1 and its teams
     * division 2 with teams => no data
     */
    public function testGettingOneDivisionWithTeamsAsDivisionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division3)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division4)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division4)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertSame([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions/' . $division4->getId() . '?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /*
     * division 1 with teams and competitions => division 1 and its competition and teams
     * division 2 with teams and competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsDivisionAdministrator(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
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
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division3)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division4 */
        $division4 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);
        $team4 = aTeam()->withName('London Trotters')->inDivision($division4)->build();
        $team5 = aTeam()->withName('Globe Trotters')->inDivision($division4)->build();

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertSame([
            'id' => $division1->getId(),
            'name' => 'MP',
            'display_order' => 1,
            'competition' => [
                'id' => $competition1->getId(),
                'name' => 'London League',
            ],
            'teams' => [
                [
                    'id' => $team1->getId(),
                    'name' => 'The Spiders',
                ],
                [
                    'id' => $team2->getId(),
                    'name' => 'Boston Bears',
                ],
            ],
        ], $data);

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);

        $response = $this->getJson('/api/v1/divisions/' . $division4->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');
        $this->assertEmpty($data);
    }

    /******************
     * Club Secretary *
     ******************/

    /* all divisions => no data */
    public function testGettingAllDivisionsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions with competitions => no data */
    public function testGettingAllDivisionsWithCompetitionsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions with teams => no data */
    public function testGettingAllDivisionsWithTeamsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions with competitions and teams => no data */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions in competition 1 => no data */
    public function testGettingAllDivisionsForOneCompetitionAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions in competition 1 with competitions => no data */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions in competition 1 with teams => no data */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions in competition 1 with competitions and teams => no data */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* division 1 => no data */
    public function testGettingOneDivisionAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* division 1 with competitions => no data */
    public function testGettingOneDivisionWithCompetitionsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* division 1 with teams => no data */
    public function testGettingOneDivisionWithTeamsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* division 1 with teams and competitions => no data */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsClubSecretary(): void
    {
        $club1 = aClub()->build();

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
        $team1 = aTeam()->inClub($club1)->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->inClub($club1)->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->inClub($club1)->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /******************
     * Team Secretary *
     ******************/

    /* all divisions => no data */
    public function testGettingAllDivisionsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions with competitions => no data */
    public function testGettingAllDivisionsWithCompetitionsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions with teams => no data */
    public function testGettingAllDivisionsWithTeamsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions with competitions and teams => no data */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions in competition 1 => no data */
    public function testGettingAllDivisionsForOneCompetitionAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions in competition 1 with competitions => no data */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions in competition 1 with teams => no data */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* all divisions in competition 1 with competitions and teams => no data */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?competition=' . $competition1->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition2->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions?competition=' . $competition3->getId() . '&with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* division 1 => no data */
    public function testGettingOneDivisionAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* division 1 with competitions => no data */
    public function testGettingOneDivisionWithCompetitionsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* division 1 with teams => no data */
    public function testGettingOneDivisionWithTeamsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    /* division 1 with teams and competitions => no data */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsTeamSecretary(): void
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
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        /** @var Competition $competition2 */
        $competition2 = factory(Competition::class)->create([
            'name' => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        /** @var Division $division2 */
        $division2 = factory(Division::class)->create([
            'name' => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order' => 10,
        ]);
        $team3 = aTeam()->withName('Hot Stuff')->inDivision($division2)->build();
        /** @var Season $season2 */
        $season2 = factory(Season::class)->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = factory(Competition::class)->create([
            'name' => 'Youth Games',
            'season_id' => $season2->getId(),
        ]);
        /** @var Division $division3 */
        $division3 = factory(Division::class)->create([
            'name' => 'WP',
            'competition_id' => $competition3->getId(),
            'display_order' => 1,
        ]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions/' . $division1->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division2->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
        $response = $this->getJson('/api/v1/divisions/' . $division3->getId() . '?with[]=competition&with[]=teams')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));
    }
}
