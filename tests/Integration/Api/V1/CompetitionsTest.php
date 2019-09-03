<?php

namespace Tests\Integration\Api\V1;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use Tests\ApiTestCase;
use Tests\AssertArrayContent;

class CompetitionsTest extends ApiTestCase
{
    use AssertArrayContent;

    public function testGettingAllCompetitions(): void
    {
        $season1 = factory(Season::class)->create();
        $competition1 = factory(Competition::class)->create([
            'name'      => 'London League',
            'season_id' => $season1->getId(),
        ]);
        $competition2 = factory(Competition::class)->create([
            'name'      => 'University Games',
            'season_id' => $season1->getId(),
        ]);
        $season2 = factory(Season::class)->create();
        $competition3 = factory(Competition::class)->create([
            'name'      => 'Minor Leagues',
            'season_id' => $season2->getId(),
        ]);

        $response = $this->get('/api/v1/competitions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(3, $data);
        $this->assertContains([
            'id'   => $competition1->getId(),
            'name' => 'London League',
        ], $data);
        $this->assertContains([
            'id'   => $competition2->getId(),
            'name' => 'University Games',
        ], $data);
        $this->assertContains([
            'id'   => $competition3->getId(),
            'name' => 'Minor Leagues',
        ], $data);
    }

    public function testGettingAllCompetitionsWithTheirSeason(): void
    {
        $season = factory(Season::class)->create(['year' => '2000']);
        $competition1 = factory(Competition::class)->create([
            'name'      => 'London League',
            'season_id' => $season->getId(),
        ]);
        $competition2 = factory(Competition::class)->create([
            'name'      => 'University Games',
            'season_id' => $season->getId(),
        ]);

        $response = $this->get('/api/v1/competitions?with[]=season')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id'     => $competition1->getId(),
            'name'   => 'London League',
            'season' => [
                'id'   => $season->getId(),
                'name' => '2000/01',
            ],
        ], $data);
        $this->assertContains([
            'id'     => $competition2->getId(),
            'name'   => 'University Games',
            'season' => [
                'id'   => $season->getId(),
                'name' => '2000/01',
            ],
        ], $data);
    }

    public function testGettingAllCompetitionsWithTheirDivisions(): void
    {
        $season = factory(Season::class)->create();
        $competition1 = factory(Competition::class)->create([
            'name'      => 'London League',
            'season_id' => $season->getId(),
        ]);
        $division1 = factory(Division::class)->create([
            'name'           => 'MP',
            'competition_id' => $competition1->getId(),
            'display_order'  => 1,
        ]);
        $division2 = factory(Division::class)->create([
            'name'           => 'WP',
            'competition_id' => $competition1->getId(),
            'display_order'  => 10,
        ]);
        $competition2 = factory(Competition::class)->create([
            'name'      => 'University Games',
            'season_id' => $season->getId(),
        ]);
        $division3 = factory(Division::class)->create([
            'name'           => 'MP',
            'competition_id' => $competition2->getId(),
            'display_order'  => 10,
        ]);
        $division4 = factory(Division::class)->create([
            'name'           => 'WP',
            'competition_id' => $competition2->getId(),
            'display_order'  => 1,
        ]);

        $response = $this->get('/api/v1/competitions?with[]=divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        foreach ($data as $competition) {
            $this->assertArrayHasKey('id', $competition);
            switch ($competition['id']) {
                case $competition1->getId():
                    $this->assertArrayHasKey('divisions', $competition);
                    $divisions = $competition['divisions'];
                    $this->assertCount(2, $divisions);
                    $this->assertContains([
                        'id'            => $division1->getId(),
                        'name'          => 'MP',
                        'display_order' => 1,
                    ], $divisions);
                    $this->assertContains([
                        'id'            => $division2->getId(),
                        'name'          => 'WP',
                        'display_order' => 10,
                    ], $divisions);
                    break;
                case $competition2->getId():
                    $this->assertArrayHasKey('divisions', $competition);
                    $divisions = $competition['divisions'];
                    $this->assertContains([
                        'id'            => $division3->getId(),
                        'name'          => 'MP',
                        'display_order' => 10,
                    ], $divisions);
                    $this->assertContains([
                        'id'            => $division4->getId(),
                        'name'          => 'WP',
                        'display_order' => 1,
                    ], $divisions);
                    break;
                default:
                    $this->assertTrue(false, "Unexpected competition {$competition['id']}");
                    break;
            }
        }
    }

    public function testGettingAllCompetitionsForOneSeason(): void
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

        $response = $this->get('/api/v1/competitions?season=' . $season->getId())
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertCount(2, $data);
        $this->assertContains([
            'id'   => $competition1->getId(),
            'name' => 'London League',
        ], $data);
        $this->assertContains([
            'id'   => $competition2->getId(),
            'name' => 'University Games',
        ], $data);
    }

    public function testGettingAllCompetitionsWhenThereAreNone(): void
    {
        $response = $this->get('/api/v1/competitions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    public function testGettingAllCompetitionsForNonExistingSeason(): void
    {
        $this->get('/api/v1/competitions?season=1')
            ->assertNotFound();
    }

    public function testGettingOneCompetition(): void
    {
        $season = factory(Season::class)->create();
        $competition = factory(Competition::class)->create([
            'name'      => 'London Magic League',
            'season_id' => $season->getId(),
        ]);

        $response = $this->get('/api/v1/competitions/' . $competition->getId())
            ->assertOk();

        $this->assertEquals([
            'id'   => $competition->getId(),
            'name' => 'London Magic League',
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingANonExistingCompetition(): void
    {
        $this->get('/api/v1/competitions/1')
            ->assertNotFound();
    }

    public function testGettingOneCompetitionWithItsSeason(): void
    {
        $season = factory(Season::class)->create(['year' => '2019']);
        $competition = factory(Competition::class)->create([
            'name'      => 'London Magic League',
            'season_id' => $season->getId(),
        ]);

        $response = $this->get('/api/v1/competitions/' . $competition->getId() . '?with[]=season')
            ->assertOk();

        $this->assertEquals([
            'id'     => $competition->getId(),
            'name'   => 'London Magic League',
            'season' => [
                'id'   => $season->getId(),
                'name' => '2019/20',
            ],
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingOneCompetitionsWithItsDivisions(): void
    {
        $competition = factory(Competition::class)->create([
            'name' => 'London Magic League',
        ]);
        $division2 = factory(Division::class)->create([
            'name'           => 'DIV1M',
            'competition_id' => $competition->getId(),
            'display_order'  => 2,
        ]);
        $division1 = factory(Division::class)->create([
            'name'           => 'MP',
            'competition_id' => $competition->getId(),
            'display_order'  => 1,
        ]);

        $response = $this->get('/api/v1/competitions/' . $competition->getId() . '?with[]=divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertArrayContent([
            'id'   => $competition->getId(),
            'name' => 'London Magic League',
        ], $data);

        $this->assertArrayHasKey('divisions', $data);
        $divisions = $data['divisions'];

        $this->assertCount(2, $divisions);
        $this->assertContains([
            'id'            => $division1->getId(),
            'name'          => 'MP',
            'display_order' => 1,
        ], $divisions);
        $this->assertContains([
            'id'            => $division2->getId(),
            'name'          => 'DIV1M',
            'display_order' => 2,
        ], $divisions);
    }

    public function testGettingOneCompetitionsWithItsDivisionsWhenThereAreNone(): void
    {
        $competition = factory(Competition::class)->create([
            'name' => 'London Magic League',
        ]);

        $response = $this->get('/api/v1/competitions/' . $competition->getId() . '?with[]=divisions')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertArrayContent([
            'id'   => $competition->getId(),
            'name' => 'London Magic League',
        ], $data);

        $this->assertArrayHasKey('divisions', $data);
        $this->assertEmpty($data['divisions']);
    }
}
