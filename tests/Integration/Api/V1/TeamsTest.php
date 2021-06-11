<?php

namespace Tests\Integration\Api\V1;

use App\Models\Club;
use App\Models\Division;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Laravel\Passport\Passport;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class TeamsTest extends TestCase
{
    use InteractsWithArrays;

    public function testGettingAllTeams(): void
    {
        $team1 = Team::factory()->create(['name' => 'Rockefeller']);
        $team3 = Team::factory()->create(['name' => 'Mighty Plumbers']);
        $team2 = Team::factory()->create(['name' => 'Sporting Dudes']);

        $response = $this->get('/api/v1/teams')
                         ->assertOk();
        $teams = $response->json('data');

        $this->assertCount(3, $teams);
        $this->assertContains(
            [
                'id' => $team1->getId(),
                'name' => 'Rockefeller',
            ],
            $teams
        );
        $this->assertContains(
            [
                'id' => $team2->getId(),
                'name' => 'Sporting Dudes',
            ],
            $teams
        );
        $this->assertContains(
            [
                'id' => $team3->getId(),
                'name' => 'Mighty Plumbers',
            ],
            $teams
        );
    }

    public function testGettingAllTeamsWhenThereAreNone(): void
    {
        $response = $this->get('/api/v1/teams')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    public function testGettingAllTeamsWithTheirClub(): void
    {
        $club1 = Club::factory()->create(['name' => 'Buildings']);
        $team1 = Team::factory()->for($club1)->create(['name' => 'Rockefeller']);
        $club3 = Club::factory()->create(['name' => 'Traders']);
        $team3 = Team::factory()->for($club3)->create(['name' => 'Mighty Plumbers']);
        $club2 = Club::factory()->create(['name' => 'Dudes']);
        $team2 = Team::factory()->for($club2)->create(['name' => 'Sporting Dudes']);

        $response = $this->get('/api/v1/teams?with[]=club')
                         ->assertOk();
        $teams = $response->json('data');

        $this->assertCount(3, $teams);
        $this->assertContains(
            [
                'id' => $team1->getId(),
                'name' => 'Rockefeller',
                'club' => [
                    'id' => $club1->getId(),
                    'name' => 'Buildings',
                ],
            ],
            $teams
        );
        $this->assertContains(
            [
                'id' => $team2->getId(),
                'name' => 'Sporting Dudes',
                'club' => [
                    'id' => $club2->getId(),
                    'name' => 'Dudes',
                ],
            ],
            $teams
        );
        $this->assertContains(
            [
                'id' => $team3->getId(),
                'name' => 'Mighty Plumbers',
                'club' => [
                    'id' => $club3->getId(),
                    'name' => 'Traders',
                ],
            ],
            $teams
        );
    }

    public function testGettingAllTeamsWithTheirVenue(): void
    {
        $venue1 = Venue::factory()->create(['name' => 'The Box']);
        $team1 = Team::factory()->for($venue1)->create(['name' => 'Rockefeller']);
        $venue2 = Venue::factory()->create(['name' => 'Sobell SC']);
        $team3 = Team::factory()->for($venue2)->create(['name' => 'Mighty Plumbers']);
        $venue3 = Venue::factory()->create(['name' => 'Westminster University Sports Hall']);
        $team2 = Team::factory()->for($venue3)->create(['name' => 'Sporting Dudes']);

        $response = $this->get('/api/v1/teams?with[]=venue')
                         ->assertOk();
        $teams = $response->json('data');

        $this->assertCount(3, $teams);
        $this->assertContains(
            [
                'id' => $team1->getId(),
                'name' => 'Rockefeller',
                'venue' => [
                    'id' => $venue1->getId(),
                    'name' => 'The Box',
                ],
            ],
            $teams
        );
        $this->assertContains(
            [
                'id' => $team2->getId(),
                'name' => 'Sporting Dudes',
                'venue' => [
                    'id' => $venue3->getId(),
                    'name' => 'Westminster University Sports Hall',
                ],
            ],
            $teams
        );
        $this->assertContains(
            [
                'id' => $team3->getId(),
                'name' => 'Mighty Plumbers',
                'venue' => [
                    'id' => $venue2->getId(),
                    'name' => 'Sobell SC',
                ],
            ],
            $teams
        );
    }

    public function testGettingAllTeamsWithTheirDivisions(): void
    {
        $division1 = Division::factory()->create(['name' => 'MP']);
        $division2 = Division::factory()->create(['name' => 'MP']);
        $division3 = Division::factory()->create(['name' => 'MP']);
        $team1 = Team::factory()->hasAttached($division1)->hasAttached($division2)->create(
            ['name' => 'Mighty Plumbers']
        );
        $team2 = Team::factory()->hasAttached($division2)->hasAttached($division3)->create(
            ['name' => 'Sporting Dudes']
        );

        $response = $this->get('/api/v1/teams?with[]=divisions')
                         ->assertOk();
        $teams = $response->json('data');

        $this->assertCount(2, $teams);
        foreach ($teams as $team) {
            $this->assertArrayHasKey('id', $team);
            switch ($team['id']) {
                case $team1->getId():
                    $this->assertArrayContent(
                        [
                            'id' => $team1->getId(),
                            'name' => 'Mighty Plumbers',
                        ],
                        $team
                    );
                    $this->assertArrayHasKey('divisions', $team);
                    foreach ($team['divisions'] as $division) {
                        $this->assertArrayHasKey('id', $division);
                        switch ($division['id']) {
                            case $division1->getId():
                                $this->assertArrayContent(
                                    [
                                        'id' => $division1->getId(),
                                        'name' => 'MP',
                                    ],
                                    $division
                                );
                                break;
                            case $division2->getId():
                                $this->assertArrayContent(
                                    [
                                        'id' => $division2->getId(),
                                        'name' => 'MP',
                                    ],
                                    $division
                                );
                                break;
                            default:
                                $this->assertTrue(
                                    false,
                                    "Unexpected division {$division['id]']} in team {$team['id']}"
                                );
                                break;
                        }
                    }
                    break;
                case $team2->getId():
                    $this->assertArrayContent(
                        [
                            'id' => $team2->getId(),
                            'name' => 'Sporting Dudes',
                        ],
                        $team
                    );
                    $this->assertArrayHasKey('divisions', $team);
                    foreach ($team['divisions'] as $division) {
                        $this->assertArrayHasKey('id', $division);
                        switch ($division['id']) {
                            case $division2->getId():
                                $this->assertArrayContent(
                                    [
                                        'id' => $division2->getId(),
                                        'name' => 'MP',
                                    ],
                                    $division
                                );
                                break;
                            case $division3->getId():
                                $this->assertArrayContent(
                                    [
                                        'id' => $division3->getId(),
                                        'name' => 'MP',
                                    ],
                                    $division
                                );
                                break;
                            default:
                                $this->assertTrue(
                                    false,
                                    "Unexpected division {$division['id]']} in team {$team['id']}"
                                );
                                break;
                        }
                    }
                    break;
                default:
                    $this->assertTrue(false, "Unexpected team {$team['id']}");
                    break;
            }
        }
    }

    public function testGettingOneTeam(): void
    {
        Team::factory()->create(['name' => 'Rockefeller']);
        $team3 = Team::factory()->create(['name' => 'Mighty Plumbers']);
        Team::factory()->create(['name' => 'Sporting Dudes']);

        $response = $this->get("/api/v1/teams/{$team3->getId()}")
                         ->assertOk();

        $this->assertSame(
            [
                'id' => $team3->getId(),
                'name' => 'Mighty Plumbers',
            ],
            $response->json('data')
        );
    }

    public function testGettingANonExistingTeam(): void
    {
        $this->get('/api/v1/teams/1')
             ->assertNotFound();
    }

    public function testGettingOneTeamWithItsClub(): void
    {
        $club = Club::factory()->create(['name' => 'Traders']);
        Team::factory()->create(['name' => 'Rockefeller']);
        $team3 = Team::factory()->for($club)->create(['name' => 'Mighty Plumbers']);
        Team::factory()->create(['name' => 'Sporting Dudes']);

        $response = $this->get("/api/v1/teams/{$team3->getId()}?with[]=club")
                         ->assertOk();

        $this->assertSame(
            [
                'id' => $team3->getId(),

                'name' => 'Mighty Plumbers',
                'club' => [
                    'id' => $club->getId(),
                    'name' => 'Traders',
                ],
            ],
            $response->json('data')
        );
    }

    public function testGettingOneTeamWithItsVenue(): void
    {
        $venue = Venue::factory()->create(['name' => 'The Plaza Gym']);
        $team1 = Team::factory()->for($venue)->create(['name' => 'Rockefeller']);
        Team::factory()->create(['name' => 'Sporting Dudes']);

        $response = $this->get("/api/v1/teams/{$team1->getId()}?with[]=venue")
                         ->assertOk();

        $this->assertSame(
            [
                'id' => $team1->getId(),
                'name' => 'Rockefeller',
                'venue' => [
                    'id' => $venue->getId(),
                    'name' => 'The Plaza Gym',
                ],
            ],
            $response->json('data')
        );
    }

    public function testGettingOneTeamWithItsClubVenue(): void
    {
        $venue = Venue::factory()->create(['name' => 'The Plaza Gym']);
        $club = Club::factory()->for($venue)->create(['name' => 'Traders']);
        Team::factory()->create(['name' => 'Rockefeller']);
        $team3 = Team::factory()->for($club)->create(['name' => 'Mighty Plumbers']);
        Team::factory()->create(['name' => 'Sporting Dudes']);

        $response = $this->get("/api/v1/teams/{$team3->getId()}?with[]=venue")
                         ->assertOk();

        $this->assertSame(
            [
                'id' => $team3->getId(),
                'name' => 'Mighty Plumbers',
                'venue' => [
                    'id' => $venue->getId(),
                    'name' => 'The Plaza Gym',
                ],
            ],
            $response->json('data')
        );
    }

    public function testGettingOneTeamWithItsDivisions(): void
    {
        $division1 = Division::factory()->create(['name' => 'MP']);
        $division2 = Division::factory()->create(['name' => 'WP']);
        $division3 = Division::factory()->create(['name' => 'MixP']);
        $team1 = Team::factory()->hasAttached($division1)->hasAttached($division2)->create(
            ['name' => 'Mighty Plumbers']
        );
        Team::factory()->hasAttached($division2)->hasAttached($division3)->create(['name' => 'Sporting Dudes']);

        $response = $this->get("/api/v1/teams/{$team1->getId()}?with[]=divisions")
                         ->assertOk();
        $data = $response->json('data');

        $this->assertArrayContent(
            [
                'id' => $team1->getId(),
                'name' => 'Mighty Plumbers',
            ],
            $data
        );

        $this->assertArrayHasKey('divisions', $data);
        foreach ($data['divisions'] as $division) {
            $this->assertArrayHasKey('id', $division);
            switch ($division['id']) {
                case $division1->getId():
                    $this->assertArrayContent(
                        [
                            'id' => $division1->getId(),
                            'name' => 'MP',
                        ],
                        $division
                    );
                    break;
                case $division2->getId():
                    $this->assertArrayContent(
                        [
                            'id' => $division2->getId(),
                            'name' => 'WP',
                        ],
                        $division
                    );
                    break;
                default:
                    $this->assertTrue(false, "Unexpected division {$division['id']} for team {$team1->getId()}");
                    break;
            }
        }
    }

    public function testGettingOneTeamWithItsDivisionsWhenThereAreNone(): void
    {
        $team1 = Team::factory()->create(['name' => 'Rockefeller']);
        Team::factory()->create(['name' => 'Mighty Plumbers']);
        Team::factory()->create(['name' => 'Sporting Dudes']);

        $response = $this->get("/api/v1/teams/{$team1->getId()}?with[]=divisions")
                         ->assertOk();

        $this->assertSame(
            [
                'id' => $team1->getId(),
                'name' => 'Rockefeller',
                'divisions' => [],
            ],
            $response->json('data')
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(User::factory()->create());
    }
}
