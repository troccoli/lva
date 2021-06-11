<?php

namespace Tests\Integration\Api\V1;

use App\Models\Club;
use App\Models\User;
use App\Models\Venue;
use Laravel\Passport\Passport;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class VenuesTest extends TestCase
{
    use InteractsWithArrays;

    public function testGettingAllVenues(): void
    {
        $venue1 = Venue::factory()->create(['name' => 'Olympic Gym']);
        $venue2 = Venue::factory()->create(['name' => 'The Box']);

        $response = $this->get('/api/v1/venues')
                         ->assertOk();
        $venues = $response->json('data');

        $this->assertCount(2, $venues);
        $this->assertContains(
            [
                'id' => $venue1->getId(),
                'name' => 'Olympic Gym',
            ],
            $venues
        );
        $this->assertContains(
            [
                'id' => $venue2->getId(),
                'name' => 'The Box',
            ],
            $venues
        );
    }

    public function testGettingAllVenuesWhenThereAreNone(): void
    {
        $response = $this->get('/api/v1/venues')
                         ->assertOk();

        $this->assertEmpty($response->json('data'));
    }

    public function testGettingAllVenusWithTheirClubs(): void
    {
        $venue1 = Venue::factory()->create(['name' => 'Olympic Gym']);
        $club1 = Club::factory()->for($venue1)->create(['name' => 'The Giants']);
        $club2 = Club::factory()->for($venue1)->create(['name' => 'The Minions']);
        $venue2 = Venue::factory()->create(['name' => 'The Box']);
        $club3 = Club::factory()->for($venue2)->create(['name' => 'London Sparrows']);
        $club4 = Club::factory()->for($venue2)->create(['name' => 'Boston Spiders']);

        $response = $this->get('/api/v1/venues?with[]=clubs')
                         ->assertOk();
        $venues = $response->json('data');

        $this->assertCount(2, $venues);
        foreach ($venues as $venue) {
            $this->assertArrayHasKey('id', $venue);
            switch ($venue['id']) {
                case $venue1->getId():
                    $this->assertArrayContent(
                        [
                            'id' => $venue1->getId(),
                            'name' => 'Olympic Gym',
                        ],
                        $venue
                    );
                    $this->assertArrayHasKey('clubs', $venue);
                    $clubs = $venue['clubs'];
                    $this->assertCount(2, $clubs);
                    $this->assertContains(
                        [
                            'id' => $club1->getId(),
                            'name' => 'The Giants',
                        ],
                        $clubs
                    );
                    $this->assertContains(
                        [
                            'id' => $club2->getId(),
                            'name' => 'The Minions',
                        ],
                        $clubs
                    );
                    break;
                case $venue2->getId():
                    $this->assertArrayContent(
                        [
                            'id' => $venue2->getId(),
                            'name' => 'The Box',
                        ],
                        $venue
                    );
                    $this->assertArrayHasKey('clubs', $venue);
                    $clubs = $venue['clubs'];
                    $this->assertCount(2, $clubs);
                    $this->assertContains(
                        [
                            'id' => $club3->getId(),
                            'name' => 'London Sparrows',
                        ],
                        $clubs
                    );
                    $this->assertContains(
                        [
                            'id' => $club4->getId(),
                            'name' => 'Boston Spiders',
                        ],
                        $clubs
                    );
                    break;
                default:
                    $this->assertTrue(false, "Unexpected venue {$venue['id']}");
                    break;
            }
        }
    }

    public function testGettingAllVenuesWithTheirClubsWhenThereAreNone(): void
    {
        $venue1 = Venue::factory()->create(['name' => 'Olympic Gym']);
        $venue2 = Venue::factory()->create(['name' => 'The Box']);

        $response = $this->get('/api/v1/venues?with[]=clubs')
                         ->assertOk();
        $venues = $response->json('data');

        $this->assertCount(2, $venues);
        foreach ($venues as $venue) {
            $this->assertArrayHasKey('id', $venue);
            switch ($venue['id']) {
                case $venue1->getId():
                    $this->assertArrayContent(
                        [
                            'id' => $venue1->getId(),
                            'name' => 'Olympic Gym',
                        ],
                        $venue
                    );
                    $this->assertArrayHasKey('clubs', $venue);
                    $this->assertEmpty($venue['clubs']);
                    break;
                case $venue2->getId():
                    $this->assertArrayContent(
                        [
                            'id' => $venue2->getId(),
                            'name' => 'The Box',
                        ],
                        $venue
                    );
                    $this->assertArrayHasKey('clubs', $venue);
                    $this->assertEmpty($venue['clubs']);
                    break;
                default:
                    $this->assertTrue(false, "Unexpected venue {$venue['id']}");
                    break;
            }
        }
    }

    public function testGettingOneVenue(): void
    {
        Venue::factory()->create(['name' => 'Olympic Gym']);
        $venue2 = Venue::factory()->create(['name' => 'The Box']);

        $response = $this->get("/api/v1/venues/{$venue2->getId()}")
                         ->assertOk();

        $this->assertSame(
            [
                'id' => $venue2->getId(),
                'name' => 'The Box',
            ],
            $response->json('data')
        );
    }

    public function testGettingANonExistingVenue(): void
    {
        $this->get('/api/v1/venues/1')->assertNotFound();
    }

    public function testGettingOneVenueWithItsClubs(): void
    {
        $venue1 = Venue::factory()->create(['name' => 'Olympic Gym']);
        $club1 = Club::factory()->for($venue1)->create(['name' => 'The Giants']);
        $club2 = Club::factory()->for($venue1)->create(['name' => 'The Minions']);
        $club3 = Club::factory()->for($venue1)->create(['name' => 'The Worker Bees']);
        Venue::factory()->create(['name' => 'The Box']);

        $response = $this->get("/api/v1/venues/{$venue1->getId()}?with[]=clubs")
                         ->assertOk();
        $venues = $response->json('data');

        $this->assertArrayContent(
            [
                'id' => $venue1->getId(),
                'name' => 'Olympic Gym',
            ],
            $venues
        );

        $this->assertArrayHasKey('clubs', $venues);
        $clubs = $venues['clubs'];

        $this->assertCount(3, $clubs);
        $this->assertContains(
            [
                'id' => $club1->getId(),
                'name' => 'The Giants',
            ],
            $clubs
        );
        $this->assertContains(
            [
                'id' => $club2->getId(),
                'name' => 'The Minions',
            ],
            $clubs
        );
        $this->assertContains(
            [
                'id' => $club3->getId(),
                'name' => 'The Worker Bees',
            ],
            $clubs
        );
    }

    public function testGettingOneVenueWithItsClubsWhenThereAreNone(): void
    {
        $venue1 = Venue::factory()->create(['name' => 'Olympic Gym']);
        Club::factory()->for($venue1)->create(['name' => 'The Giants']);
        Club::factory()->for($venue1)->create(['name' => 'The Minions']);
        Club::factory()->for($venue1)->create(['name' => 'The Worker Bees']);
        $venue2 = Venue::factory()->create(['name' => 'The Box']);

        $response = $this->get("/api/v1/venues/{$venue2->getId()}?with[]=clubs")
                         ->assertOk();
        $venues = $response->json('data');

        $this->assertArrayContent(
            [
                'id' => $venue2->getId(),
                'name' => 'The Box',
            ],
            $venues
        );

        $this->assertArrayHasKey('clubs', $venues);
        $this->assertEmpty($venues['clubs']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Passport::actingAs(User::factory()->create());
    }
}
