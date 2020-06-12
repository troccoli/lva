<?php

namespace Tests\Integration\Api\V1;

use App\Models\Venue;
use Laravel\Passport\Passport;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class ClubsTest extends TestCase
{
    use InteractsWithArrays;

    public function testGettingAllClubs(): void
    {
        $club1 = aClub()->withName('London Bears')->build();
        $club2 = aClub()->withName('The Spiders')->build();
        $club3 = aClub()->withName('Boston Giants')->build();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/clubs')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(3, $data);
        $this->assertContains([
            'id'   => $club1->getId(),
            'name' => 'London Bears',
        ], $data);
        $this->assertContains([
            'id'   => $club2->getId(),
            'name' => 'The Spiders',
        ], $data);
        $this->assertContains([
            'id'   => $club3->getId(),
            'name' => 'Boston Giants',
        ], $data);
    }

    public function testGettingAllClubsWithTheirVenues(): void
    {
        $venue1 = factory(Venue::class)->create(['name' => 'Olympic Stadium']);
        $club1 = aClub()->withName('London Bears')->withVenue($venue1)->build();
        $club2 = aClub()->withName('The Spiders')->withVenue($venue1)->build();
        $venue2 = factory(Venue::class)->create(['name' => 'The Box']);
        $club3 = aClub()->withName('Boston Giants')->withVenue($venue2)->build();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/clubs?with[]=venue')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(3, $data);
        $this->assertContains([
            'id'    => $club1->getId(),
            'name'  => 'London Bears',
            'venue' => [
                'id'   => $venue1->getId(),
                'name' => 'Olympic Stadium',
            ],
        ], $data);
        $this->assertContains([
            'id'    => $club2->getId(),
            'name'  => 'The Spiders',
            'venue' => [
                'id'   => $venue1->getId(),
                'name' => 'Olympic Stadium',
            ],
        ], $data);
        $this->assertContains([
            'id'    => $club3->getId(),
            'name'  => 'Boston Giants',
            'venue' => [
                'id'   => $venue2->getId(),
                'name' => 'The Box',
            ],
        ], $data);
    }

    public function testGettingAllClubsWithTheirTeams(): void
    {
        $club1 = aClub()->withName('London Bears')->build();
        $team1 = aTeam()->withName('Big Bears')->inClub($club1)->build();
        $club2 = aClub()->withName('The Spiders')->build();
        $club3 = aClub()->withName('Boston Giants')->build();
        $team2 = aTeam()->withName('BigFoot')->inClub($club3)->build();
        $team3 = aTeam()->withName('King Kong')->inClub($club3)->build();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/clubs?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(3, $data);
        foreach ($data as $club) {
            $this->assertArrayHasKey('id', $club);
            switch ($club['id']) {
                case $club1->getId():
                    $this->assertArrayHasKey('teams', $club);
                    $teams = $club['teams'];
                    $this->assertCount(1, $teams);
                    $this->assertContains([
                        'id'   => $team1->getId(),
                        'name' => 'Big Bears',
                    ], $teams);
                    break;
                case $club2->getId():
                    $this->assertArrayHasKey('teams', $club);
                    $teams = $club['teams'];
                    $this->assertEmpty($teams);
                    break;
                case $club3->getId():
                    $this->assertArrayHasKey('teams', $club);
                    $teams = $club['teams'];
                    $this->assertCount(2, $teams);
                    $this->assertContains([
                        'id'   => $team2->getId(),
                        'name' => 'BigFoot',
                    ], $teams);
                    $this->assertContains([
                        'id'   => $team3->getId(),
                        'name' => 'King Kong',
                    ], $teams);
                    break;
                default:
                    $this->assertTrue(false, "Unexpected club {$club['id']}");
                    break;
            }
        }
    }

    public function testGettingAllClubsWhenThereAreNone(): void
    {
        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/clubs')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    public function testGettingOneClub(): void
    {
        $club = aClub()->withName('Paris St. German')->build();
        aClub()->build();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/clubs/' . $club->getId())
            ->assertOk();

        $this->assertEquals([
            'id'   => $club->getId(),
            'name' => 'Paris St. German',
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingANonExistingClub(): void
    {
        Passport::actingAs($this->siteAdmin);

        $this->getJson('/api/v1/clubs/1')
            ->assertNotFound();
    }

    public function testGettingOneClubWithItsVenue(): void
    {
        $venue = factory(Venue::class)->create(['name' => 'Sobell SC']);
        $club = aClub()->withName('Paris St. German')->withVenue($venue)->build();
        aClub()->build();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/clubs/' . $club->getId() . '?with[]=venue')
            ->assertOk();

        $this->assertEquals([
            'id'    => $club->getId(),
            'name'  => 'Paris St. German',
            'venue' => [
                'id'   => $venue->getId(),
                'name' => 'Sobell SC',
            ],
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingOneClubWithItsTeams(): void
    {
        $club = aClub()->withName('Paris St. German')->build();
        $team1 = aTeam()->withName('Spikers')->inClub($club)->build();
        $team2 = aTeam()->withName('Fireball')->inClub($club)->build();
        aClub()->build();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/clubs/' . $club->getId() . '?with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');

        $this->assertArrayContent([
            'id' => $club->getId(),
            'name' => 'Paris St. German',
        ], $data);

        $this->assertArrayHasKey('teams', $data);
        $teams = $data['teams'];

        $this->assertCount(2, $teams);
        $this->assertContains([
            'id'   => $team1->getId(),
            'name' => 'Spikers',
        ], $teams);
        $this->assertContains([
            'id'   => $team2->getId(),
            'name' => 'Fireball',
        ], $teams);
    }
}
