<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\FixtureResource;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class FixtureResourceTest extends TestCase
{
    use RefreshDatabase, InteractsWithArrays;

    private $division;
    private $homeTeam;
    private $awayTeam;
    private $venue;
    /** @var Fixture */
    private $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = factory(Division::class)->create(['name' => 'Super 8']);
        $this->homeTeam = aTeam()->withName('Home Team')->build();
        $this->awayTeam = aTeam()->withName('Away Team')->build();
        $this->venue = factory(Venue::class)->create();

        $this->fixture = aFixture()
            ->inDivision($this->division)
            ->between($this->homeTeam, $this->awayTeam)
            ->at($this->venue)
            ->build();
    }

    public function testItReturnTheCorrectFields(): void
    {
        $request = \Mockery::mock(Request::class);

        $resource = new FixtureResource($this->fixture);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'             => $this->fixture->getId(),
            'number'         => $this->fixture->getMatchNumber(),
            'division'       => $this->fixture->getDivision()->getName(),
            'division_id'    => $this->fixture->getDivision()->getId(),
            'home_team'      => $this->fixture->getHomeTeam()->getName(),
            'home_team_id'   => $this->fixture->getHomeTeam()->getId(),
            'away_team'      => $this->fixture->getAwayTeam()->getName(),
            'away_team_id'   => $this->fixture->getAwayTeam()->getId(),
            'date'           => $this->fixture->getMatchDate()->toDateString(),
            'time'           => $this->fixture->getMatchTime()->format('H:i'),
            'venue'          => $this->fixture->getVenue()->getName(),
            'venue_id'       => $this->fixture->getVenue()->getId(),
        ], $resourceArray);
    }
}
