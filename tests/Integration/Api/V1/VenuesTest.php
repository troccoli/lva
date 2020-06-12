<?php

namespace Tests\Integration\Api\V1;

use App\Models\Venue;
use Laravel\Passport\Passport;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class VenuesTest extends TestCase
{
    use InteractsWithArrays;

    public function testGettingAllVenues(): void
    {
        $venue1 = factory(Venue::class)->create(['name' => 'Olympic Gym']);
        $venue2 = factory(Venue::class)->create(['name' => 'The Box']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/venues')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(2, $data);
        $this->assertContains([
            'id'   => $venue1->getId(),
            'name' => 'Olympic Gym',
        ], $data);
        $this->assertContains([
            'id'   => $venue2->getId(),
            'name' => 'The Box',
        ], $data);
    }

    public function testGettingAllVenuesWhenThereAreNone(): void
    {
        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/venues')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    public function testGettingAllVenusWithTheirClubs(): void
    {
        $venue1 = factory(Venue::class)->create(['name' => 'Olympic Gym']);
        $club1 = aClub()->withName('The Giants')->withVenue($venue1)->build();
        $club2 = aClub()->withName('The Minions')->withVenue($venue1)->build();
        $venue2 = factory(Venue::class)->create(['name' => 'The Box']);
        $club3 = aClub()->withName('London Sparrows')->withVenue($venue2)->build();
        $club4 = aClub()->withName('Boston Spiders')->withVenue($venue2)->build();

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/venues?with[]=clubs')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(2, $data);
        foreach ($data as $venue) {
            $this->assertArrayHasKey('id', $venue);
            switch ($venue['id']) {
                case $venue1->getId():
                    $this->assertArrayContent([
                        'id'   => $venue1->getId(),
                        'name' => 'Olympic Gym',
                    ], $venue);
                    $this->assertArrayHasKey('clubs', $venue);
                    $clubs = $venue['clubs'];
                    $this->assertCount(2, $clubs);
                    $this->assertContains([
                        'id'   => $club1->getId(),
                        'name' => 'The Giants',
                    ], $clubs);
                    $this->assertContains([
                        'id'   => $club2->getId(),
                        'name' => 'The Minions',
                    ], $clubs);
                    break;
                case $venue2->getId():
                    $this->assertArrayContent([
                        'id'   => $venue2->getId(),
                        'name' => 'The Box',
                    ], $venue);
                    $this->assertArrayHasKey('clubs', $venue);
                    $clubs = $venue['clubs'];
                    $this->assertCount(2, $clubs);
                    $this->assertContains([
                        'id'   => $club3->getId(),
                        'name' => 'London Sparrows',
                    ], $clubs);
                    $this->assertContains([
                        'id'   => $club4->getId(),
                        'name' => 'Boston Spiders',
                    ], $clubs);
                    break;
                default:
                    $this->assertTrue(false, "Unexpected venue {$venue['id']}");
                    break;
            }
        }
    }

    public function testGettingAllVenuesWithTheirClubsWhenThereAreNone(): void
    {
        $venue1 = factory(Venue::class)->create(['name' => 'Olympic Gym']);
        $venue2 = factory(Venue::class)->create(['name' => 'The Box']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/venues?with[]=clubs')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(2, $data);
        foreach ($data as $venue) {
            $this->assertArrayHasKey('id', $venue);
            switch ($venue['id']) {
                case $venue1->getId():
                    $this->assertArrayContent([
                        'id'   => $venue1->getId(),
                        'name' => 'Olympic Gym',
                    ], $venue);
                    $this->assertArrayHasKey('clubs', $venue);
                    $this->assertEmpty($venue['clubs']);
                    break;
                case $venue2->getId():
                    $this->assertArrayContent([
                        'id'   => $venue2->getId(),
                        'name' => 'The Box',
                    ], $venue);
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
        $venue1 = factory(Venue::class)->create(['name' => 'Olympic Gym']);
        $venue2 = factory(Venue::class)->create(['name' => 'The Box']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/venues/' . $venue2->getId())
            ->assertOk();

        $this->assertSame([
            'id'   => $venue2->getId(),
            'name' => 'The Box',
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingANonExistingVenue(): void
    {
        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/venues/1')
            ->assertNotFound();
    }

    public function testGettingOneVenueWithItsClubs(): void
    {
        $venue1 = factory(Venue::class)->create(['name' => 'Olympic Gym']);
        $club1 = aClub()->withName('The Giants')->withVenue($venue1)->build();
        $club2 = aClub()->withName('The Minions')->withVenue($venue1)->build();
        $club3 = aClub()->withName('The Worker Bees')->withVenue($venue1)->build();
        $venue2 = factory(Venue::class)->create(['name' => 'The Box']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/venues/' . $venue1->getId() . '?with[]=clubs')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertArrayContent([
            'id'   => $venue1->getId(),
            'name' => 'Olympic Gym',
        ], $data);

        $this->assertArrayHasKey('clubs', $data);
        $clubs = $data['clubs'];

        $this->assertCount(3, $clubs);
        $this->assertContains([
            'id'   => $club1->getId(),
            'name' => 'The Giants',
        ], $clubs);
        $this->assertContains([
            'id'   => $club2->getId(),
            'name' => 'The Minions',
        ], $clubs);
        $this->assertContains([
            'id'   => $club3->getId(),
            'name' => 'The Worker Bees',
        ], $clubs);
    }

    public function testGettingOneVenueWithItsClubsWhenThereAreNone(): void
    {
        $venue1 = factory(Venue::class)->create(['name' => 'Olympic Gym']);
        $club1 = aClub()->withName('The Giants')->withVenue($venue1)->build();
        $club2 = aClub()->withName('The Minions')->withVenue($venue1)->build();
        $club3 = aClub()->withName('The Worker Bees')->withVenue($venue1)->build();
        $venue2 = factory(Venue::class)->create(['name' => 'The Box']);

        Passport::actingAs($this->siteAdmin);

        $response = $this->getJson('/api/v1/venues/' . $venue2->getId() . '?with[]=clubs')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertArrayContent([
            'id'   => $venue2->getId(),
            'name' => 'The Box',
        ], $data);

        $this->assertArrayHasKey('clubs', $data);
        $this->assertEmpty($data['clubs']);
    }
}
