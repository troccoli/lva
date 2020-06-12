<?php

namespace Tests\Integration\Api\V1;

use App\Models\Competition;
use App\Models\Division;
use Tests\ApiTestCase;
use Tests\Concerns\InteractsWithArrays;

class DivisionsTest extends ApiTestCase
{
    use InteractsWithArrays;

    public function testGettingAllDivisions(): void
    {
        $division1 = factory(Division::class)->create([
            'name'          => 'DIV1M',
            'display_order' => 2,
        ]);
        $division2 = factory(Division::class)->create([
            'name'          => 'DIV1M',
            'display_order' => 2,
        ]);
        $division3 = factory(Division::class)->create([
            'name'          => 'MP',
            'display_order' => 1,
        ]);

        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();

        $data = $response->decodeResponseJson('data');

        $this->assertCount(3, $data);
        $this->assertContains([
            'id'            => $division1->getId(),
            'name'          => 'DIV1M',
            'display_order' => 2,
        ], $data);
        $this->assertContains([
            'id'            => $division3->getId(),
            'name'          => 'MP',
            'display_order' => 1,
        ], $data);
        $this->assertContains([
            'id'            => $division2->getId(),
            'name'          => 'DIV1M',
            'display_order' => 2,
        ], $data);
    }

    public function testGettingAllDivisionsWhenThereAreNone(): void
    {
        $response = $this->getJson('/api/v1/divisions')
            ->assertOk();

        $this->assertEmpty($response->decodeResponseJson('data'));
    }

    public function testGettingAllDivisionsWithTheirCompetition(): void
    {
        $competition1 = factory(Competition::class)->create(['name' => 'London League']);
        $division1 = factory(Division::class)->create([
            'name'           => 'DIV1M',
            'display_order'  => 2,
            'competition_id' => $competition1->getId(),
        ]);
        $competition2 = factory(Competition::class)->create(['name' => 'UniGames']);
        $division2 = factory(Division::class)->create([
            'name'           => 'DIV1M',
            'display_order'  => 2,
            'competition_id' => $competition2->getId(),
        ]);
        $division3 = factory(Division::class)->create([
            'name'           => 'MP',
            'display_order'  => 1,
            'competition_id' => $competition2->getId(),
        ]);

        $response = $this->getJson('/api/v1/divisions?with[]=competition')
            ->assertOk();

        $data = $response->decodeResponseJson('data');

        $this->assertCount(3, $data);
        $this->assertContains([
            'id'            => $division1->getId(),
            'name'          => 'DIV1M',
            'display_order' => 2,
            'competition'   => [
                'id'   => $competition1->getId(),
                'name' => 'London League',
            ],
        ], $data);
        $this->assertContains([
            'id'            => $division3->getId(),
            'name'          => 'MP',
            'display_order' => 1,
            'competition'   => [
                'id'   => $competition2->getId(),
                'name' => 'UniGames',
            ],
        ], $data);
        $this->assertContains([
            'id'            => $division2->getId(),
            'name'          => 'DIV1M',
            'display_order' => 2,
            'competition'   => [
                'id'   => $competition2->getId(),
                'name' => 'UniGames',
            ],
        ], $data);
    }

    public function testGettingAllDivisionsWithTheirTeams(): void
    {
        $division1 = factory(Division::class)->create([
            'name'          => 'DIV1M',
            'display_order' => 2,
        ]);
        $team1 = aTeam()->withName('The Spiders')->inDivision($division1)->build();
        $team2 = aTeam()->withName('Boston Bears')->inDivision($division1)->build();
        $division2 = factory(Division::class)->create([
            'name'          => 'MP',
            'display_order' => 1,
        ]);
        $team3 = aTeam()->withName('London Cubs')->inDivision($division2)->build();

        $response = $this->getJson('/api/v1/divisions?with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');

        $this->assertCount(2, $data);
        foreach ($data as $division) {
            $this->assertArrayHasKey('id', $division);
            switch ($division['id']) {
                case $division1->getId():
                    $this->assertArrayHasKey('teams', $division);
                    $teams = $division['teams'];
                    $this->assertCount(2, $teams);
                    $this->assertContains([
                        'id'   => $team1->getId(),
                        'name' => 'The Spiders',
                    ], $teams);
                    $this->assertContains([
                        'id'   => $team2->getId(),
                        'name' => 'Boston Bears',
                    ], $teams);
                    break;
                case $division2->getId():
                    $this->assertArrayHasKey('teams', $division);
                    $teams = $division['teams'];
                    $this->assertCount(1, $teams);
                    $this->assertContains([
                        'id'   => $team3->getId(),
                        'name' => 'London Cubs',
                    ], $teams);
                    break;
                default:
                    $this->assertTrue(false, "Unexpected division {$division['id']}");
                    break;
            }
        }
    }

    public function testGettingOneDivision(): void
    {
        $competition = factory(Competition::class)->create();
        $division = factory(Division::class)->create([
            'name'           => 'DIV1M',
            'display_order'  => 2,
            'competition_id' => $competition->getId(),
        ]);

        $response = $this->getJson('/api/v1/divisions/' . $division->getId())
            ->assertOk();

        $this->assertSame([
            'id'            => $division->getId(),
            'name'          => 'DIV1M',
            'display_order' => 2,
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingANonExistingDivision(): void
    {
        $this->getJson('/api/v1/divisions/1')
            ->assertNotFound();
    }

    public function testGettingOneDivisionWithItsCompetition(): void
    {
        $competition = factory(Competition::class)->create(['name' => 'UniGames']);
        $division = factory(Division::class)->create([
            'name'           => 'DIV1M',
            'display_order'  => 2,
            'competition_id' => $competition->getId(),
        ]);

        $response = $this->getJson('/api/v1/divisions/' . $division->getId() . '?with[]=competition')
            ->assertOk();

        $this->assertSame([
            'id'            => $division->getId(),
            'name'          => 'DIV1M',
            'display_order' => 2,
            'competition'   => [
                'id'   => $competition->getId(),
                'name' => 'UniGames',
            ],
        ], $response->decodeResponseJson('data'));
    }

    public function testGettingOneDivisionWithItsTeams(): void
    {
        $division = factory(Division::class)->create([
            'name'          => 'MP',
            'display_order' => 1,
        ]);
        $team1 = aTeam()->withName('London Bears')->inDivision($division)->build();
        $team2 = aTeam()->withName('The Giants')->inDivision($division)->build();

        $response = $this->getJson('/api/v1/divisions/' . $division->getId() . '?with[]=teams')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertArrayContent([
            'id'            => $division->getId(),
            'name'          => 'MP',
            'display_order' => 1,
        ], $data);

        $this->assertArrayHasKey('teams', $data);
        $teams = $data['teams'];

        $this->assertCount(2, $teams);
        $this->assertContains([
            'id'   => $team1->getId(),
            'name' => 'London Bears',
        ], $teams);
        $this->assertContains([
            'id'   => $team2->getId(),
            'name' => 'The Giants',
        ], $teams);
    }

    public function testGettingOneDivisionWithItsTeamsWhenThereAreNone(): void
    {
        $division = factory(Division::class)->create();

        $response = $this->getJson('/api/v1/divisions/' . $division->getId() . '?with[]=teams')
            ->assertOk();

        $data = $response->decodeResponseJson('data');
        $this->assertArrayHasKey('teams', $data);
        $this->assertEmpty($data['teams']);
    }
}
