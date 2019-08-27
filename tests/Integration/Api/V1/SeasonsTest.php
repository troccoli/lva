<?php

namespace Tests\Integration\Api\V1;

use App\Models\Competition;
use App\Models\Season;
use Tests\ApiTestCase;

class SeasonsTest extends ApiTestCase
{
    public function testGettingAllSeasons(): void
    {
        $season1 = factory(Season::class)->create(['year' => 1999]);
        $season2 = factory(Season::class)->create(['year' => 2000]);
        $season3 = factory(Season::class)->create(['year' => 2001]);
        $season4 = factory(Season::class)->create(['year' => 1998]);

        $response = $this->get('/api/v1/seasons')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(4, $data);
        $this->assertContains([
            'id'   => $season1->getId(),
            'name' => '1999/00',
        ], $data);
        $this->assertContains([
            'id'   => $season2->getId(),
            'name' => '2000/01',
        ], $data);
        $this->assertContains([
            'id'   => $season3->getId(),
            'name' => '2001/02',
        ], $data);
        $this->assertContains([
            'id'   => $season4->getId(),
            'name' => '1998/99',
        ], $data);
    }

    public function testGettingAllSeasonsWhenThereAreNone(): void
    {
        $response = $this->get('/api/v1/seasons')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    public function testGettingOneSeason(): void
    {
        $season = factory(Season::class)->create(['year' => 2000]);

        $response = $this->get('/api/v1/seasons/' . $season->getId())
            ->assertOk();

        $this->assertEquals([
            'id'   => $season->getId(),
            'name' => '2000/01',
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingANonExistingSeason(): void
    {
        $this->get('/api/v1/seasons/1')
            ->assertNotFound();
    }

    public function testGettingOneSeasonWithItsCompetitions(): void
    {
        $season = factory(Season::class)->create();

        $competition1 = factory(Competition::class)->create([
            'name'      => 'London League',
            'season_id' => $season->getId(),
        ]);
        $competition2 = factory(Competition::class)->create([
            'name'      => 'University Games',
            'season_id' => $season->getId(),
        ]);

        $response = $this->get('/api/v1/seasons/' . $season->getId() . '?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertArrayHasKey('competitions', $data);

        $competitions = $data['competitions'];
        $this->assertCount(2, $competitions);
        $this->assertContains([
            'id'        => $competition1->getId(),
            'name'      => 'London League',
        ], $competitions);
        $this->assertContains([
            'id'        => $competition2->getId(),
            'name'      => 'University Games',
        ], $competitions);
    }

    public function testGettingOneSeasonWithItsCompetitionsWhenThereAreNone(): void
    {
        $season = factory(Season::class)->create();

        $response = $this->get('/api/v1/seasons/' . $season->getId() . '?with[]=competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertArrayHasKey('competitions', $data);
        $this->assertEmpty($data['competitions']);
    }
}
