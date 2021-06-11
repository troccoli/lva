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

class DivisionsTest extends TestCase
{
    use InteractsWithArrays;

    public function testGettingAllDivisionsWhenThereAreNone(): void
    {
        Passport::actingAs($this->siteAdmin);

        $response = $this->get('/api/v1/divisions')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
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
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(
            [
                'name' => 'Youth Games',
                'season_id' => $season2->getId(),
            ]
        );
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(5, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division5->getId(),
                'name' => 'WP',
                'display_order' => 1,
            ],
            $divisions
        );
    }

    /* all divisions with competitions => all divisions and their competition */
    public function testGettingAllDivisionsWithCompetitionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(
            [
                'name' => 'Youth Games',
                'season_id' => $season2->getId(),
            ]
        );
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(5, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division5->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition3->getId(),
                    'name' => 'Youth Games',
                ],
            ],
            $divisions
        );
    }

    /* all divisions with teams => all divisions and their teams */
    public function testGettingAllDivisionsWithTeamsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(3, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'teams' => [
                    [
                        'id' => $team3->getId(),
                        'name' => 'Hot Stuff',
                    ],
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'teams' => [],
            ],
            $divisions
        );
    }

    /* all divisions with competitions and teams => all divisions and their competition and teams */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(3, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition3->getId(),
                    'name' => 'Youth Games',
                ],
                'teams' => [],
            ],
            $divisions
        );
    }

    /* all divisions in competition 1 => all divisions in competition 1 */
    public function testGettingAllDivisionsForOneCompetitionAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
            ],
            $divisions
        );
    }

    /* all divisions in competition 1 with competitions => all divisions in competitions 1 and their competition 1 */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=competition")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );
    }

    /* all divisions in competition 1 with teams => all divisions in competition 1 and their teams */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=teams")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'teams' => [
                    [
                        'id' => $team3->getId(),
                        'name' => 'Hot Stuff',
                    ],
                ],
            ],
            $divisions
        );
    }

    /* all divisions in competition 1 with competitions and teams =>  all divisions in competitions 1 and their competition 1 and teams */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition1->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
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
            ],
            $divisions
        );
    }

    /* division 1 => division 1 */
    public function testGettingOneDivisionAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division2->getId(),
                'name' => 'MP',
                'display_order' => 10,
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division3->getId(),
                'name' => 'WP',
                'display_order' => 1,
            ],
            $response->json('data')
        );
    }

    /* division 1 with competitions => division 1 and its competition */
    public function testGettingOneDivisionWithCompetitionsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division2->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division3->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition3->getId(),
                    'name' => 'Youth Games',
                ],
            ],
            $response->json('data')
        );
    }

    /* division 1 with teams => division 1 and its teams */
    public function testGettingOneDivisionWithTeamsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team2 = Team::factory()->hasAttached($division2)->create(['name' => 'The Bears']);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'The Dolphins']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        $team4 = Team::factory()->hasAttached($division3)->create(['name' => 'The Bats']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'teams' => [
                    [
                        'id' => $team1->getId(),
                        'name' => 'The Spiders',
                    ],
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
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
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division3->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'teams' => [
                    [
                        'id' => $team4->getId(),
                        'name' => 'The Bats',
                    ],
                ],
            ],
            $response->json('data')
        );
    }

    /* division 1 with teams and competitions => division 1 and its competition and teams */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsSiteAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team2 = Team::factory()->hasAttached($division2)->create(['name' => 'The Bears']);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'The Dolphins']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        $team4 = Team::factory()->hasAttached($division3)->create(['name' => 'The Bats']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
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
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
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
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
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
            ],
            $response->json('data')
        );
    }

    /************************
     * Season Administrator *
     ************************/

    /* all divisions => all divisions in season 1 */
    public function testGettingAllDivisionsAsSeasonAdministrator(): void
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
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(4, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division5->getId(),
                'name' => 'WP',
                'display_order' => 1,
            ],
            $divisions
        );
    }

    /* all divisions with competitions => all divisions in season 1 and their competition */
    public function testGettingAllDivisionsWithCompetitionsAsSeasonAdministrator(): void
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
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();

        $division = $response->json('data');
        $this->assertCount(4, $division);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $division
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $division
        );
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $division
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $division
        );

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();

        $division = $response->json('data');
        $this->assertCount(1, $division);
        $this->assertContains(
            [
                'id' => $division5->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition3->getId(),
                    'name' => 'Youth Games',
                ],
            ],
            $division
        );
    }

    /* all divisions with teams => all divisions in season 1 and their teams */
    public function testGettingAllDivisionsWithTeamsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        /** @var Season $season2 */
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        $team4 = Team::factory()->hasAttached($division3)->create(['name' => 'London Trotters']);
        $team5 = Team::factory()->hasAttached($division3)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'teams' => [
                    [
                        'id' => $team3->getId(),
                        'name' => 'Hot Stuff',
                    ],
                ],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
    }

    /* all divisions with competitions and teams => all divisions in season 1 and their competition and teams */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        /** @var Season $season2 */
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        $team4 = Team::factory()->hasAttached($division3)->create(['name' => 'London Trotters']);
        $team5 = Team::factory()->hasAttached($division3)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
    }

    /*
     * all divisions in competition 1 => all divisions in competition 1
     * all divisions in competition 2 => no data
    */
    public function testGettingAllDivisionsForOneCompetitionAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * all divisions in competition 1 with competitions => all divisions in competitions 1 and their competition 1
     * all divisions in competition 2 with competitions => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=competition")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=competition")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=competition")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * all divisions in competition 1 with teams => all divisions in competition 1 and their teams
     * all divisions in competition 2 with teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        Team::factory()->hasAttached($division3)->create(['name' => 'London Trotters']);
        Team::factory()->hasAttached($division3)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=teams")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=teams")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'teams' => [
                    [
                        'id' => $team3->getId(),
                        'name' => 'Hot Stuff',
                    ],
                ],
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=teams")
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * all divisions in competition 1 with competitions and teams => all divisions in competitions 1 and their competition 1 and teams
     * all divisions in competition 2 with competitions and teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        Team::factory()->hasAttached($division3)->create(['name' => 'London Trotters']);
        Team::factory()->hasAttached($division3)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition1->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition2->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition3->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();

        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * division 1 => division 1
     * division 2 => division no data
     */
    public function testGettingOneDivisionAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division5->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /*
     * division 1 with competitions => division 1 and its competition
     * division 2 with competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division5->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /*
     * division 1 with teams => division 1 and its teams
     * division 2 with teams => no data
     */
    public function testGettingOneDivisionWithTeamsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team2 = Team::factory()->hasAttached($division3)->create(['name' => 'The Bears']);
        $team3 = Team::factory()->hasAttached($division3)->create(['name' => 'The Dolphins']);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        Team::factory()->hasAttached($division5)->create(['name' => 'The Bats']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'teams' => [
                    [
                        'id' => $team1->getId(),
                        'name' => 'The Spiders',
                    ],
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
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
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division5->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /*
    * division 1 with teams and competitions => division 1 and its competition and teams
    * division 2 with teams and competitions => no data
    */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsSeasonAdministrator(): void
    {
        /** @var Season $season1 */
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team2 = Team::factory()->hasAttached($division3)->create(['name' => 'The Bears']);
        $team3 = Team::factory()->hasAttached($division3)->create(['name' => 'The Dolphins']);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        Team::factory()->hasAttached($division5)->create(['name' => 'The Bats']);

        Passport::actingAs($this->userWithRole(RolesHelper::seasonAdmin($season1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
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
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
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
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division5->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /*****************************
     * Competition Administrator *
     *****************************/

    /* all divisions => all divisions in competition 1 */
    public function testGettingAllDivisionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        /** @var Competition $competition2 */
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition2)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition3)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division5->getId(),
                'name' => 'WP',
                'display_order' => 1,
            ],
            $divisions
        );
    }

    /* all divisions with competitions => all divisions in competition 1 and their competition */
    public function testGettingAllDivisionsWithCompetitionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        /** @var Competition $competition2 */
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division5->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition3->getId(),
                    'name' => 'Youth Games',
                ],
            ],
            $divisions
        );
    }

    /* all divisions with teams => all divisions in competition 1 and their teams */
    public function testGettingAllDivisionsWithTeamsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        /** @var Competition $competition2 */
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        $team4 = Team::factory()->hasAttached($division5)->create(['name' => 'London Trotters']);
        $team5 = Team::factory()->hasAttached($division5)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'teams' => [
                    [
                        'id' => $team3->getId(),
                        'name' => 'Hot Stuff',
                    ],
                ],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition2)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'teams' => [],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'teams' => [],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition3)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
    }

    /* all divisions with competitions and teams => all divisions in competition 1 and their competition and teams */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        /** @var Competition $competition2 */
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $division4 = Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);
        $season2 = Season::factory()->create(['year' => 2001]);
        /** @var Competition $competition3 */
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division5 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        $team4 = Team::factory()->hasAttached($division5)->create(['name' => 'London Trotters']);
        $team5 = Team::factory()->hasAttached($division5)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
                'teams' => [],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
                'teams' => [],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
    }

    /*
     * all divisions in competition 1 => all divisions in competition 1
     * all divisions in competition 2 => no data
     */
    public function testGettingAllDivisionsForOneCompetitionAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * all divisions in competition 1 with competitions => all divisions in competitions 1 and their competition 1
     * all divisions in competition 2 with competitions => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=competition")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=competition")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * all divisions in competition 1 with teams => all divisions in competition 1 and their teams
     * all divisions in competition 2 with teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->hasAttached($division3)->create(['name' => 'London Trotters']);
        Team::factory()->hasAttached($division3)->create(['name' => 'Globe Trotters']);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'teams' => [],
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * all divisions in competition 1 with competitions and teams => all divisions in competitions 1 and their competition 1 and teams
     * all divisions in competition 2 with competitions and teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->hasAttached($division3)->create(['name' => 'London Trotters']);
        Team::factory()->hasAttached($division3)->create(['name' => 'Globe Trotters']);
        Division::factory()->for($competition2)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition1->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(2, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
                'teams' => [],
            ],
            $divisions
        );

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition2->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * division 1 => division 1
     * division 2 => division no data
     */
    public function testGettingOneDivisionAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /*
     * division 1 with competitions => division 1 and its competition
     * division 2 with competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /*
     * division 1 with teams => division 1 and its teams
     * division 2 with teams => no data
     */
    public function testGettingOneDivisionWithTeamsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
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
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'teams' => [],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /*
     * division 1 with teams and competitions => division 1 and its competition and teams
     * division 2 with teams and competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsCompetitionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        /** @var Competition $competition1 */
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);

        Passport::actingAs($this->userWithRole(RolesHelper::competitionAdmin($competition1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
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
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEquals(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
                'teams' => [],
            ],
            $response->json('data')
        );

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /**************************
     * Division Administrator *
     **************************/

    /* all divisions => only division 1 */
    public function testGettingAllDivisionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Division $division2 */
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        /** @var Division $division3 */
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        /** @var Division $division4 */
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division2)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division3)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division4)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
            ],
            $divisions
        );
    }

    /* all divisions with competitions => only division 1 and its competition */
    public function testGettingAllDivisionsWithCompetitionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Division $division2 */
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        /** @var Division $division3 */
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        /** @var Division $division4 */
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition2->getId(),
                    'name' => 'University Games',
                ],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division4)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division4->getId(),
                'name' => 'WP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition3->getId(),
                    'name' => 'Youth Games',
                ],
            ],
            $divisions
        );
    }

    /* all divisions with teams => only division 1 and its teams */
    public function testGettingAllDivisionsWithTeamsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        /** @var Division $division2 */
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        /** @var Division $division3 */
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division3)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        /** @var Division $division4 */
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        $team4 = Team::factory()->hasAttached($division4)->create(['name' => 'London Trotters']);
        $team5 = Team::factory()->hasAttached($division4)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division2)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'teams' => [],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division3)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division3->getId(),
                'name' => 'MP',
                'display_order' => 10,
                'teams' => [
                    [
                        'id' => $team3->getId(),
                        'name' => 'Hot Stuff',
                    ],
                ],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division4)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
    }

    /* all divisions with competitions and teams => only division 1 and its competition and teams */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        /** @var Division $division2 */
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        /** @var Division $division3 */
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $team3 = Team::factory()->hasAttached($division3)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        /** @var Division $division4 */
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        $team4 = Team::factory()->hasAttached($division4)->create(['name' => 'London Trotters']);
        $team5 = Team::factory()->hasAttached($division4)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division2->getId(),
                'name' => 'WP',
                'display_order' => 10,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
                'teams' => [],
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division4)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );
    }

    /*
     * all divisions in competition 1 => only division 1
     * all divisions in competition 2 => no data
     */
    public function testGettingAllDivisionsForOneCompetitionAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * all divisions in competition 1 with competitions => only division 1 and its competition
     * all divisions in competition 2 with competitions => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=competition")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=competition")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=competition")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * all divisions in competition 1 with teams => only division 1 and its teams
     * all divisions in competition 2 with teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->hasAttached($division3)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        Team::factory()->hasAttached($division4)->create(['name' => 'London Trotters']);
        Team::factory()->hasAttached($division4)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * all divisions in competition 1 with competitions and teams => only division 1 and its competition and teams
     * all divisions in competition 2 with competitions and teams => no data
     */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->hasAttached($division3)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        Team::factory()->hasAttached($division4)->create(['name' => 'London Trotters']);
        Team::factory()->hasAttached($division4)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition1->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertCount(1, $divisions);
        $this->assertContains(
            [
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
            ],
            $divisions
        );

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition2->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition3->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * division 1 => division 1
     * division 2 => no data
     */
    public function testGettingOneDivisionAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertSame(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions/{$division4->getId()}")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * division 1 with competitions => division 1 and its competition
     * division 2 with competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertSame(
            [
                'id' => $division1->getId(),
                'name' => 'MP',
                'display_order' => 1,
                'competition' => [
                    'id' => $competition1->getId(),
                    'name' => 'London League',
                ],
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions/{$division4->getId()}?with[]=competition")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * division 1 with teams => division 1 and its teams
     * division 2 with teams => no data
     */
    public function testGettingOneDivisionWithTeamsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->hasAttached($division3)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        Team::factory()->hasAttached($division4)->create(['name' => 'London Trotters']);
        Team::factory()->hasAttached($division4)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertSame(
            [
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
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions/{$division4->getId()}?with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /*
     * division 1 with teams and competitions => division 1 and its competition and teams
     * division 2 with teams and competitions => no data
     */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsDivisionAdministrator(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        /** @var Division $division1 */
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $division2 = Division::factory()->for($competition1)->create(['name' => 'WP', 'display_order' => 10]);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division3 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->hasAttached($division3)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division4 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);
        Team::factory()->hasAttached($division4)->create(['name' => 'London Trotters']);
        Team::factory()->hasAttached($division4)->create(['name' => 'Globe Trotters']);

        Passport::actingAs($this->userWithRole(RolesHelper::divisionAdmin($division1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertSame(
            [
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
            ],
            $divisions
        );

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);

        $response = $this->getJson("/api/v1/divisions/{$division4->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $divisions = $response->json('data');
        $this->assertEmpty($divisions);
    }

    /******************
     * Club Secretary *
     ******************/

    /* all divisions => no data */
    public function testGettingAllDivisionsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions with competitions => no data */
    public function testGettingAllDivisionsWithCompetitionsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions with teams => no data */
    public function testGettingAllDivisionsWithTeamsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions with competitions and teams => no data */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions in competition 1 => no data */
    public function testGettingAllDivisionsForOneCompetitionAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions in competition 1 with competitions => no data */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions in competition 1 with teams => no data */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions in competition 1 with competitions and teams => no data */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition1->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition2->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition3->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* division 1 => no data */
    public function testGettingOneDivisionAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* division 1 with competitions => no data */
    public function testGettingOneDivisionWithCompetitionsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* division 1 with teams => no data */
    public function testGettingOneDivisionWithTeamsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* division 1 with teams and competitions => no data */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsClubSecretary(): void
    {
        /** @var Club $club1 */
        $club1 = Club::factory()->create();

        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'The Spiders']);
        Team::factory()->for($club1)->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        Team::factory()->for($club1)->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::clubSecretary($club1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /******************
     * Team Secretary *
     ******************/

    /* all divisions => no data */
    public function testGettingAllDivisionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions with competitions => no data */
    public function testGettingAllDivisionsWithCompetitionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions with teams => no data */
    public function testGettingAllDivisionsWithTeamsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions with competitions and teams => no data */
    public function testGettingAllDivisionsWithCompetitionsAndTeamsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson('/api/v1/divisions?with[]=competition&with[]=teams')
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions in competition 1 => no data */
    public function testGettingAllDivisionsForOneCompetitionAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions in competition 1 with competitions => no data */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions in competition 1 with teams => no data */
    public function testGettingAllDivisionsForOneCompetitionWithTeamsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson("/api/v1/divisions?competition={$competition1->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition2->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions?competition={$competition3->getId()}&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* all divisions in competition 1 with competitions and teams => no data */
    public function testGettingAllDivisionsForOneCompetitionWithCompetitionsAndTeamsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition1->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition2->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition3->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition1->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition2->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition3->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition1->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition2->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson(
            "/api/v1/divisions?competition={$competition3->getId()}&with[]=competition&with[]=teams"
        )
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* division 1 => no data */
    public function testGettingOneDivisionAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* division 1 with competitions => no data */
    public function testGettingOneDivisionWithCompetitionsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* division 1 with teams => no data */
    public function testGettingOneDivisionWithTeamsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var true $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }

    /* division 1 with competitions and teams => no data */
    public function testGettingOneDivisionWithCompetitionsAndTeamsAsTeamSecretary(): void
    {
        $season1 = Season::factory()->create(['year' => 2000]);
        $competition1 = Competition::factory()->for($season1)->create(['name' => 'London League']);
        $division1 = Division::factory()->for($competition1)->create(['name' => 'MP', 'display_order' => 1]);
        /** @var Team $team1 */
        $team1 = Team::factory()->hasAttached($division1)->create(['name' => 'The Spiders']);
        /** @var Team $team2 */
        $team2 = Team::factory()->hasAttached($division1)->create(['name' => 'Boston Bears']);
        $competition2 = Competition::factory()->for($season1)->create(['name' => 'University Games']);
        $division2 = Division::factory()->for($competition2)->create(['name' => 'MP', 'display_order' => 10]);
        /** @var Team $team3 */
        $team3 = Team::factory()->hasAttached($division2)->create(['name' => 'Hot Stuff']);
        $season2 = Season::factory()->create(['year' => 2001]);
        $competition3 = Competition::factory()->for($season2)->create(['name' => 'Youth Games']);
        $division3 = Division::factory()->for($competition3)->create(['name' => 'WP', 'display_order' => 1]);

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team1)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team2)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));

        Passport::actingAs($this->userWithRole(RolesHelper::teamSecretary($team3)));

        $response = $this->getJson("/api/v1/divisions/{$division1->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division2->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
        $response = $this->getJson("/api/v1/divisions/{$division3->getId()}?with[]=competition&with[]=teams")
                         ->assertOk();
        $this->assertEmpty($response->json('data'));
    }
}
