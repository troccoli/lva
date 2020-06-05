<?php

namespace Tests\Integration\Api\V1;

use App\Models\Division;
use App\Models\Venue;
use Tests\ApiTestCase;
use Tests\Concerns\InteractsWithArrays;

class TeamsTest extends ApiTestCase
{
    use InteractsWithArrays;

    public function testGettingAllTeams(): void
    {
        $team1 = aTeam()->withName('Rocketfella')->build();
        $team3 = aTeam()->withName('Mighty Plumbers')->build();
        $team2 = aTeam()->withName('Sporting Dudes')->build();

        $response = $this->get('/api/v1/teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(3, $data);
        $this->assertContains([
            'id'   => $team1->getId(),
            'name' => 'Rocketfella',
        ], $data);
        $this->assertContains([
            'id'   => $team2->getId(),
            'name' => 'Sporting Dudes',
        ], $data);
        $this->assertContains([
            'id'   => $team3->getId(),
            'name' => 'Mighty Plumbers',
        ], $data);
    }

    public function testGettingAllTeamsWhenThereAreNone(): void
    {
        $response = $this->get('/api/v1/teams')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    public function testGettingAllTeamsWithTheirClub(): void
    {
        $club1 = aClub()->withName('Buildings')->build();
        $team1 = aTeam()->withName('Rocketfella')->inClub($club1)->build();
        $club3 = aClub()->withName('Traders')->build();
        $team3 = aTeam()->withName('Mighty Plumbers')->inClub($club3)->build();
        $club2 = aClub()->withName('Dudes')->build();
        $team2 = aTeam()->withName('Sporting Dudes')->inClub($club2)->build();

        $response = $this->get('/api/v1/teams?with[]=club')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(3, $data);
        $this->assertContains([
            'id'   => $team1->getId(),
            'name' => 'Rocketfella',
            'club' => [
                'id'   => $club1->getId(),
                'name' => 'Buildings',
            ],
        ], $data);
        $this->assertContains([
            'id'   => $team2->getId(),
            'name' => 'Sporting Dudes',
            'club' => [
                'id'   => $club2->getId(),
                'name' => 'Dudes',
            ],
        ], $data);
        $this->assertContains([
            'id'   => $team3->getId(),
            'name' => 'Mighty Plumbers',
            'club' => [
                'id'   => $club3->getId(),
                'name' => 'Traders',
            ],
        ], $data);
    }

    public function testGettingAllTeamsWithTheirVenue(): void
    {
        $venue1 = factory(Venue::class)->create(['name' => 'The Box']);
        $team1 = aTeam()->withName('Rocketfella')->withVenue($venue1)->build();
        $venue2 = factory(Venue::class)->create(['name' => 'Sobell SC']);
        $team3 = aTeam()->withName('Mighty Plumbers')->withVenue($venue2)->build();
        $venue3 = factory(Venue::class)->create(['name' => 'Westminster University Sports Hall']);
        $team2 = aTeam()->withName('Sporting Dudes')->withVenue($venue3)->build();

        $response = $this->get('/api/v1/teams?with[]=venue')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(3, $data);
        $this->assertContains([
            'id'    => $team1->getId(),
            'name'  => 'Rocketfella',
            'venue' => [
                'id'   => $venue1->getId(),
                'name' => 'The Box',
            ],
        ], $data);
        $this->assertContains([
            'id'    => $team2->getId(),
            'name'  => 'Sporting Dudes',
            'venue' => [
                'id'   => $venue3->getId(),
                'name' => 'Westminster University Sports Hall',
            ],
        ], $data);
        $this->assertContains([
            'id'    => $team3->getId(),
            'name'  => 'Mighty Plumbers',
            'venue' => [
                'id'   => $venue2->getId(),
                'name' => 'Sobell SC',
            ],
        ], $data);
    }

    public function testGettingAllTeamsWithTheirDivisions(): void
    {
        $division1 = factory(Division::class)->create(['name' => 'MP']);
        $division2 = factory(Division::class)->create(['name' => 'MP']);
        $division3 = factory(Division::class)->create(['name' => 'MP']);
        $team1 = aTeam()->withName('Mighty Plumbers')
            ->inDivision($division1)
            ->inDivision($division2)
            ->build();
        $team2 = aTeam()->withName('Sporting Dudes')
            ->inDivision($division2)
            ->inDivision($division3)
            ->build();

        $response = $this->get('/api/v1/teams?with[]=divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(2, $data);
        foreach ($data as $team) {
            $this->assertArrayHasKey('id', $team);
            switch ($team['id']) {
                case $team1->getId():
                    $this->assertArrayContent([
                        'id'   => $team1->getId(),
                        'name' => 'Mighty Plumbers',
                    ], $team);
                    $this->assertArrayHasKey('divisions', $team);
                    foreach ($team['divisions'] as $division) {
                        $this->assertArrayHasKey('id', $division);
                        switch ($division['id']) {
                            case $division1->getId():
                                $this->assertArrayContent([
                                    'id'   => $division1->getId(),
                                    'name' => 'MP',
                                ], $division);
                                break;
                            case $division2->getId():
                                $this->assertArrayContent([
                                    'id'   => $division2->getId(),
                                    'name' => 'MP',
                                ], $division);
                                break;
                            default:
                                $this->assertTrue(false,
                                    "Unexpected division {$division['id]']} in team {$team['id']}");
                                break;
                        }
                    }
                    break;
                case $team2->getId():
                    $this->assertArrayContent([
                        'id'   => $team2->getId(),
                        'name' => 'Sporting Dudes',
                    ], $team);
                    $this->assertArrayHasKey('divisions', $team);
                    foreach ($team['divisions'] as $division) {
                        $this->assertArrayHasKey('id', $division);
                        switch ($division['id']) {
                            case $division2->getId():
                                $this->assertArrayContent([
                                    'id'   => $division2->getId(),
                                    'name' => 'MP',
                                ], $division);
                                break;
                            case $division3->getId():
                                $this->assertArrayContent([
                                    'id'   => $division3->getId(),
                                    'name' => 'MP',
                                ], $division);
                                break;
                            default:
                                $this->assertTrue(false,
                                    "Unexpected division {$division['id]']} in team {$team['id']}");
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
        $team1 = aTeam()->withName('Rocketfella')->build();
        $team3 = aTeam()->withName('Mighty Plumbers')->build();
        $team2 = aTeam()->withName('Sporting Dudes')->build();

        $response = $this->get('/api/v1/teams/' . $team3->getId())
            ->assertOk();

        $this->assertSame([
            'id'   => $team3->getId(),
            'name' => 'Mighty Plumbers',
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingANonExistingTeam(): void
    {
        $this->get('/api/v1/teams/1')
            ->assertNotFound();
    }

    public function testGettingOneTeamWithItsClub(): void
    {
        $club = aClub()->withName('Traders')->build();
        $team1 = aTeam()->withName('Rocketfella')->build();
        $team3 = aTeam()->withName('Mighty Plumbers')->inClub($club)->build();
        $team2 = aTeam()->withName('Sporting Dudes')->build();

        $response = $this->get('/api/v1/teams/' . $team3->getId() . '?with[]=club')
            ->assertOk();

        $this->assertSame([
            'id' => $team3->getId(),

            'name' => 'Mighty Plumbers',
            'club' => [
                'id'   => $club->getId(),
                'name' => 'Traders',
            ],
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingOneTeamWithItsVenue(): void
    {
        $venue = factory(Venue::class)->create(['name' => 'The Plaza Gym']);
        $team1 = aTeam()->withName('Rocketfella')->withVenue($venue)->build();
        $team2 = aTeam()->withName('Sporting Dudes')->build();

        $response = $this->get('/api/v1/teams/' . $team1->getId() . '?with[]=venue')
            ->assertOk();

        $this->assertSame([
            'id'    => $team1->getId(),
            'name'  => 'Rocketfella',
            'venue' => [
                'id'   => $venue->getId(),
                'name' => 'The Plaza Gym',
            ],
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingOneTeamWithItsClubVenue(): void
    {
        $venue = factory(Venue::class)->create(['name' => 'The Plaza Gym']);
        $club = aClub()->withName('Traders')->withVenue($venue)->build();
        $team1 = aTeam()->withName('Rocketfella')->build();
        $team3 = aTeam()->withName('Mighty Plumbers')->inClub($club)->build();
        $team2 = aTeam()->withName('Sporting Dudes')->build();

        $response = $this->get('/api/v1/teams/' . $team3->getId() . '?with[]=venue')
            ->assertOk();

        $this->assertSame([
            'id'    => $team3->getId(),
            'name'  => 'Mighty Plumbers',
            'venue' => [
                'id'   => $venue->getId(),
                'name' => 'The Plaza Gym',
            ],
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingOneTeamWithItsDivisions(): void
    {
        $division1 = factory(Division::class)->create(['name' => 'MP']);
        $division2 = factory(Division::class)->create(['name' => 'WP']);
        $division3 = factory(Division::class)->create(['name' => 'MixP']);
        $team1 = aTeam()->withName('Mighty Plumbers')
            ->inDivision($division1)
            ->inDivision($division2)
            ->build();
        $team2 = aTeam()->withName('Sporting Dudes')
            ->inDivision($division2)
            ->inDivision($division3)
            ->build();

        $response = $this->get('/api/v1/teams/' . $team1->getId() . '?with[]=divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertArrayContent([
            'id'   => $team1->getId(),
            'name' => 'Mighty Plumbers',
        ], $data);

        $this->assertArrayHasKey('divisions', $data);
        foreach ($data['divisions'] as $division) {
            $this->assertArrayHasKey('id', $division);
            switch ($division['id']) {
                case $division1->getId():
                    $this->assertArrayContent([
                        'id'   => $division1->getId(),
                        'name' => 'MP',
                    ], $division);
                    break;
                case $division2->getId():
                    $this->assertArrayContent([
                        'id'   => $division2->getId(),
                        'name' => 'WP',
                    ], $division);
                    break;
                default:
                    $this->assertTrue(false, "Unexpected division {$division['id']} for team {$team1->getId()}");
                    break;
            }
        }
    }

    public function testGettingOneTeamWithItsDivisionsWhenThereAreNone(): void
    {
        $team1 = aTeam()->withName('Rocketfella')->build();
        $team3 = aTeam()->withName('Mighty Plumbers')->build();
        $team2 = aTeam()->withName('Sporting Dudes')->build();

        $response = $this->get('/api/v1/teams/' . $team1->getId() . '?with[]=divisions')
            ->assertOk();

        $this->assertSame([
            'id'        => $team1->getId(),
            'name'      => 'Rocketfella',
            'divisions' => [],
        ], $response->decodeResponseJson('data'));
    }
}
