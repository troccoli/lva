<?php

namespace Tests\Integration\Api\V1;

use App\Models\Club;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Laravel\Passport\Passport;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class ClubsTest extends TestCase
{
    use InteractsWithArrays;

    public function testGettingAllClubs(): void
    {
        $club1 = Club::factory()->create(['name' => 'London Bears']);
        $club2 = Club::factory()->create(['name' => 'The Spiders']);
        $club3 = Club::factory()->create(['name' => 'Boston Giants']);

        $response = $this->get('/api/v1/clubs')
                         ->assertOk();
        $clubs = $response->json('data');

        $this->assertCount(3, $clubs);
        $this->assertContains(
            [
                'id' => $club1->getId(),
                'name' => 'London Bears',
            ],
            $clubs
        );
        $this->assertContains(
            [
                'id' => $club2->getId(),
                'name' => 'The Spiders',
            ],
            $clubs
        );
        $this->assertContains(
            [
                'id' => $club3->getId(),
                'name' => 'Boston Giants',
            ],
            $clubs
        );
    }

    public function testGettingAllClubsWithTheirVenues(): void
    {
        $venue1 = Venue::factory()->create(['name' => 'Olympic Stadium']);
        $club1 = Club::factory()->for($venue1)->create(['name' => 'London Bears']);
        $club2 = Club::factory()->for($venue1)->create(['name' => 'The Spiders']);
        $venue2 = Venue::factory()->create(['name' => 'The Box']);
        $club3 = Club::factory()->for($venue2)->create(['name' => 'Boston Giants']);

        $response = $this->get('/api/v1/clubs?with[]=venue')
                         ->assertOk();
        $clubs = $response->json('data');

        $this->assertCount(3, $clubs);
        $this->assertContains(
            [
                'id' => $club1->getId(),
                'name' => 'London Bears',
                'venue' => [
                    'id' => $venue1->getId(),
                    'name' => 'Olympic Stadium',
                ],
            ],
            $clubs
        );
        $this->assertContains(
            [
                'id' => $club2->getId(),
                'name' => 'The Spiders',
                'venue' => [
                    'id' => $venue1->getId(),
                    'name' => 'Olympic Stadium',
                ],
            ],
            $clubs
        );
        $this->assertContains(
            [
                'id' => $club3->getId(),
                'name' => 'Boston Giants',
                'venue' => [
                    'id' => $venue2->getId(),
                    'name' => 'The Box',
                ],
            ],
            $clubs
        );
    }

    public function testGettingAllClubsWithTheirTeams(): void
    {
        $club1 = Club::factory()->create(['name' => 'London Bears']);
        $team1 = Team::factory()->for($club1)->create(['name' => 'Big Bears']);
        $club2 = Club::factory()->create(['name' => 'The Spiders']);
        $club3 = Club::factory()->create(['name' => 'Boston Giants']);
        $team2 = Team::factory()->for($club3)->create(['name' => 'BigFoot']);
        $team3 = Team::factory()->for($club3)->create(['name' => 'King Kong']);

        $response = $this->get('/api/v1/clubs?with[]=teams')
                         ->assertOk();
        $clubs = $response->json('data');

        $this->assertCount(3, $clubs);
        foreach ($clubs as $club) {
            $this->assertArrayHasKey('id', $club);
            switch ($club['id']) {
                case $club1->getId():
                    $this->assertArrayHasKey('teams', $club);
                    $teams = $club['teams'];
                    $this->assertCount(1, $teams);
                    $this->assertContains(
                        [
                            'id' => $team1->getId(),
                            'name' => 'Big Bears',
                        ],
                        $teams
                    );
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
                    $this->assertContains(
                        [
                            'id' => $team2->getId(),
                            'name' => 'BigFoot',
                        ],
                        $teams
                    );
                    $this->assertContains(
                        [
                            'id' => $team3->getId(),
                            'name' => 'King Kong',
                        ],
                        $teams
                    );
                    break;
                default:
                    $this->assertTrue(false, "Unexpected club {$club['id']}");
                    break;
            }
        }
    }

    public function testGettingAllClubsWhenThereAreNone(): void
    {
        $response = $this->get('/api/v1/clubs')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    public function testGettingOneClub(): void
    {
        $club = Club::factory()->create(['name' => 'Paris St. German']);
        Club::factory()->create();

        $response = $this->get("/api/v1/clubs/{$club->getId()}")
                         ->assertOk();

        $clubs = $response->json('data');

        $this->assertEquals(
            [
                'id' => $club->getId(),
                'name' => 'Paris St. German',
            ],
            $clubs
        );
    }

    public function testGettingANonExistingClub(): void
    {
        $this->get('/api/v1/clubs/1')
             ->assertNotFound();
    }

    public function testGettingOneClubWithItsVenue(): void
    {
        $venue = Venue::factory()->create(['name' => 'Sobell SC']);
        $club = Club::factory()->for($venue)->create(['name' => 'Paris St. German']);
        Club::factory()->create();

        $response = $this->get("/api/v1/clubs/{$club->getId()}?with[]=venue")
                         ->assertOk();

        $clubs = $response->json('data');

        $this->assertEquals(
            [
                'id' => $club->getId(),
                'name' => 'Paris St. German',
                'venue' => [
                    'id' => $venue->getId(),
                    'name' => 'Sobell SC',
                ],
            ],
            $clubs
        );
    }

    public function testGettingOneClubWithItsTeams(): void
    {
        $club = Club::factory()->create(['name' => 'Paris St. German']);
        $team1 = Team::factory()->for($club)->create(['name' => 'Spikers']);
        $team2 = Team::factory()->for($club)->create(['name' => 'Fireball']);
        Club::factory()->create();

        $response = $this->get("/api/v1/clubs/{$club->getId()}?with[]=teams")
                         ->assertOk();

        $clubs = $response->json('data');

        $this->assertArrayContent(
            [
                'id' => $club->getId(),
                'name' => 'Paris St. German',
            ],
            $clubs
        );

        $this->assertArrayHasKey('teams', $clubs);
        $teams = $clubs['teams'];

        $this->assertCount(2, $teams);
        $this->assertContains(
            [
                'id' => $team1->getId(),
                'name' => 'Spikers',
            ],
            $teams
        );
        $this->assertContains(
            [
                'id' => $team2->getId(),
                'name' => 'Fireball',
            ],
            $teams
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(User::factory()->create());
    }
}
