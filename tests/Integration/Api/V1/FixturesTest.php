<?php

namespace Tests\Integration\Api\V1;

use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Support\Carbon;
use Tests\ApiTestCase;
use Tests\Concerns\InteractsWithArrays;

class FixturesTest extends ApiTestCase
{
    use InteractsWithArrays;

    private $season1;
    private $season2;

    private $competition1;
    private $competition2;
    private $competition3;

    private $division1;
    private $division2;
    private $division3;

    private $team1;
    private $team2;
    private $team3;
    private $team4;
    private $team5;
    private $team6;

    private $venue1;
    private $venue2;
    private $venue3;
    private $venue4;

    private $fixture1;
    private $fixture2;
    private $fixture3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->season1 = factory(Season::class)->create();
        $this->season2 = factory(Season::class)->create();

        $this->competition1 = factory(Competition::class)->create(['season_id' => $this->season1->getId()]);
        $this->competition2 = factory(Competition::class)->create(['season_id' => $this->season2->getId()]);
        $this->competition3 = factory(Competition::class)->create();

        $this->division1 = factory(Division::class)->create([
            'name'           => 'Super 8',
            'competition_id' => $this->competition1->getId(),
        ]);
        $this->division2 = factory(Division::class)->create([
            'name'           => 'WP',
            'competition_id' => $this->competition2->getId(),
        ]);
        $this->division3 = factory(Division::class)->create();

        $this->venue1 = factory(Venue::class)->create(['name' => 'Olympic Gym']);
        $this->venue2 = factory(Venue::class)->create(['name' => 'Westminster University Sports Hall']);
        $this->venue3 = factory(Venue::class)->create(['name' => 'MIT Sport Center']);
        $this->venue4 = factory(Venue::class)->create();

        $this->team1 = aTeam()->withName('London Spikers')->build();
        $this->team2 = aTeam()->withName('Boston Giants')->build();
        $this->team3 = aTeam()->withName('The Krackens')->build();
        $this->team4 = aTeam()->withName('Fireballs')->build();
        $this->team5 = aTeam()->withName('The Winner Takes It All')->build();
        $this->team6 = aTeam()->build();
    }

    public function testGettingAllFixtures(): void
    {
        $this->createRoundRobinFixtures();

        $response = $this->get('/api/v1/fixtures')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(30, $data);
    }

    public function testGettingAllFixturesPaginated(): void
    {
        // Add some extra fixtures so we can have a few pages
        $this->createRoundRobinFixtures();

        $response = $this->get('/api/v1/fixtures?page=1')
            ->assertOk();

        $this->assertCount(10, $response->decodeResponseJson('data'));
        $this->assertArrayContent([
            'current_page' => 1,
            'per_page'     => 10,
            'last_page'    => 3,
            'from'         => 1,
            'to'           => 10,
            'total'        => 30,
        ], $response->decodeResponseJson('meta'));

        $response = $this->get('/api/v1/fixtures?perPage=15')
            ->assertOk();

        $this->assertCount(15, $response->decodeResponseJson('data'));
        $this->assertArrayContent([
            'current_page' => 1,
            'per_page'     => 15,
            'last_page'    => 2,
            'from'         => 1,
            'to'           => 15,
            'total'        => 30,
        ], $response->decodeResponseJson('meta'));
    }

    public function testGettingAllFixturesInOneSeason(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?season=0')
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?season=' . $this->season1->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(2, $data);
        $this->assertDataContainsFixture1($data);
        $this->assertDataContainsFixture2($data);
    }

    public function testGettingAllFixturesInOneCompetition(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?competition=0')
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?competition=' . $this->competition3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?competition=' . $this->competition2->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture3($data);
    }

    public function testGettingAllFixturesInOneDivision(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?division=0')
            ->assertNotFound();

        $respone = $this->get('/api/v1/fixtures?division=' . $this->division3->getId())
            ->assertOk();
        $this->assertEmpty($respone->decodeResponseJson('data'));

        $respone = $this->get('/api/v1/fixtures?division=' . $this->division1->getId())
            ->assertOk();
        $data = $respone->decodeResponseJson('data');

        $this->assertCount(2, $data);
        $this->assertDataContainsFixture1($data);
        $this->assertDataContainsFixture2($data);
    }

    public function testGettingAllFixturesOnOneDate(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?on=01-01-2019')
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?on=2019-01-01')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?on=2019-05-18')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture2($data);
    }

    public function testGettingAllFixturesInOneVenue(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?venue=0')
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?venue=' . $this->venue4->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?venue=' . $this->venue1->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture2($data);
    }

    public function testGettingAllFixturesInOneVenueOnOneDate(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?venue=0&on=01-01-2019')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?venue=' . $this->venue4->getId() . '&on=01-01-2019')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?venue=0&on=2019-01-01')
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?venue=' . $this->venue4->getId() . '&on=2019-01-01')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?venue=' . $this->venue3->getId() . '&on=2019-01-01')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?venue=' . $this->venue4->getId() . '&on=2019-06-09')
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?venue=' . $this->venue3->getId() . '&on=2019-06-09')
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture1($data);
    }

    public function testGettingAllFixturesInOneVenueInOneDivision(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?division=0&venue=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?division=' . $this->division3->getId() . '&venue=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?division=0&venue=' . $this->venue4->getId())
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?division=' . $this->division3->getId() . '&venue=' . $this->venue4->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?division=' . $this->division2->getId() . '&venue=' . $this->venue4->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?division=' . $this->division3->getId() . '&venue=' . $this->venue2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?division=' . $this->division2->getId() . '&venue=' . $this->venue2->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture3($data);
    }

    public function testGettingAllFixturesInOneVenueInOneCompetition(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?competition=0&venue=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?competition=' . $this->competition1->getId() . '&venue=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?competition=0&venue=' . $this->venue2->getId())
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?competition=' . $this->competition1->getId() . '&venue=' . $this->venue2->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?competition=' . $this->competition2->getId() . '&venue=' . $this->venue1->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?competition=' . $this->competition1->getId() . '&venue=' . $this->venue1->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture2($data);
    }

    public function testGettingAllFixturesForOneTeam(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?team=0')
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?team=' . $this->team6->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?team=' . $this->team4->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(2, $data);
        $this->assertDataContainsFixture1($data);
        $this->assertDataContainsFixture2($data);
    }

    public function testGettingAllFixturesForOneTeamAtHome(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?homeTeam=0')
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?homeTeam=' . $this->team6->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?homeTeam=' . $this->team4->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture2($data);
    }

    public function testGettingAllFixturesForOneTeamAway(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?awayTeam=0')
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?awayTeam=' . $this->team6->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?awayTeam=' . $this->team4->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture1($data);
    }

    public function testGettingAllFixturesForOneTeamInOneDivision(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?division=0&team=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?division=' . $this->division3->getId() . '&team=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?division=0&team=' . $this->venue4->getId())
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?division=' . $this->division1->getId() . '&team=' . $this->team3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?division=' . $this->division1->getId() . '&team=' . $this->team1->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture1($data);
    }

    public function testGettingAllFixturesForOneTeamAtHomeInOneDivision(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?division=0&homeTeam=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?division=' . $this->division3->getId() . '&homeTeam=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?division=0&homeTeam=' . $this->venue4->getId())
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?division=' . $this->division1->getId() . '&homeTeam=' . $this->team3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?division=' . $this->division1->getId() . '&homeTeam=' . $this->team4->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture2($data);
    }

    public function testGettingAllFixturesForOneTeamAwayInOneDivision(): void
    {
        $this->createSampleFixtures();

        $this->get('/api/v1/fixtures?division=0&awayTeam=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?division=' . $this->division3->getId() . '&awayTeam=0')
            ->assertNotFound();

        $this->get('/api/v1/fixtures?division=0&awayTeam=' . $this->venue4->getId())
            ->assertNotFound();

        $response = $this->get('/api/v1/fixtures?division=' . $this->division1->getId() . '&awayTeam=' . $this->team3->getId())
            ->assertOk();
        $this->assertEmpty($response->decodeResponseJson('data'));

        $response = $this->get('/api/v1/fixtures?division=' . $this->division1->getId() . '&awayTeam=' . $this->team4->getId())
            ->assertOk();
        $data = $response->decodeResponseJson('data');

        $this->assertCount(1, $data);
        $this->assertDataContainsFixture1($data);
    }

    private function assertDataContainsFixture1($data): void
    {
        $this->assertContains([
            'id'           => $this->fixture1->getId(),
            'number'       => 45,
            'division_id'  => $this->division1->getId(),
            'division'     => 'Super 8',
            'date'         => '2019-06-09',
            'time'         => '19:15',
            'home_team_id' => $this->team1->getId(),
            'home_team'    => 'London Spikers',
            'away_team_id' => $this->team4->getId(),
            'away_team'    => 'Fireballs',
            'venue_id'     => $this->venue3->getId(),
            'venue'        => 'MIT Sport Center',
        ], $data);
    }

    private function assertDataContainsFixture2($data): void
    {
        $this->assertContains([
            'id'           => $this->fixture2->getId(),
            'number'       => 12,
            'division_id'  => $this->division1->getId(),
            'division'     => 'Super 8',
            'date'         => '2019-05-18',
            'time'         => '12:00',
            'home_team_id' => $this->team4->getId(),
            'home_team'    => 'Fireballs',
            'away_team_id' => $this->team2->getId(),
            'away_team'    => 'Boston Giants',
            'venue_id'     => $this->venue1->getId(),
            'venue'        => 'Olympic Gym',
        ], $data);
    }

    private function assertDataContainsFixture3($data): void
    {
        $this->assertContains([
            'id'           => $this->fixture3->getId(),
            'number'       => 3,
            'division_id'  => $this->division2->getId(),
            'division'     => 'WP',
            'date'         => '2019-10-20',
            'time'         => '20:00',
            'home_team_id' => $this->team3->getId(),
            'home_team'    => 'The Krackens',
            'away_team_id' => $this->team5->getId(),
            'away_team'    => 'The Winner Takes It All',
            'venue_id'     => $this->venue2->getId(),
            'venue'        => 'Westminster University Sports Hall',
        ], $data);
    }

    private function createSampleFixtures(): void
    {
        $this->fixture1 = aFixture()
            ->inDivision($this->division1)
            ->on(Carbon::parse('2019-06-09'), Carbon::parse('19:15'))
            ->number(45)
            ->between($this->team1, $this->team4)
            ->at($this->venue3)
            ->build();
        $this->fixture2 = aFixture()
            ->inDivision($this->division1)
            ->on(Carbon::parse('2019-05-18'), Carbon::parse('12:00'))
            ->number(12)
            ->between($this->team4, $this->team2)
            ->at($this->venue1)
            ->build();
        $this->fixture3 = aFixture()
            ->inDivision($this->division2)
            ->on(Carbon::parse('2019-10-20'), Carbon::parse('8:00pm'))
            ->number(3)
            ->between($this->team3, $this->team5)
            ->at($this->venue2)
            ->build();
    }

    private function createRoundRobinFixtures(): void
    {
        $homeTeams = collect([
            $this->team1,
            $this->team2,
            $this->team3,
            $this->team4,
            $this->team5,
            $this->team6,
        ]);
        $awayTeams = collect($homeTeams->all());

        $matchDate = Carbon::today();
        $matchNumber = 1;
        $homeTeams->each(function (Team $homeTeam) use ($awayTeams, &$matchDate, &$matchNumber): void {
            $awayTeams->each(function (Team $awayTeam) use ($homeTeam, &$matchDate, &$matchNumber): void {
                if ($homeTeam->getId() != $awayTeam->getId()) {
                    aFixture()
                        ->inDivision($this->division3)
                        ->number($matchNumber++)
                        ->on($matchDate->addDay(), $matchDate)
                        ->between($homeTeam, $awayTeam)
                        ->at($this->venue4)
                        ->build();
                }
            });
        });
    }
}
