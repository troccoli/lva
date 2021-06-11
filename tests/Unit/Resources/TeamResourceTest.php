<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\ClubResource;
use App\Http\Resources\DivisionResource;
use App\Http\Resources\TeamResource;
use App\Http\Resources\VenueResource;
use App\Models\Club;
use App\Models\Division;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Event;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class TeamResourceTest extends TestCase
{
    use InteractsWithArrays, WithoutEvents;

    private $team;

    public function testItReturnTheCorrectFields(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => [],
            ]
        );

        $resourceArray = (new TeamResource($this->team))->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->team->getId(),
                'name' => 'London Bears',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(MissingValue::class, $resourceArray['club']);
        $this->assertInstanceOf(MissingValue::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['divisions']);
        $this->assertEquals(DivisionResource::class, $resourceArray['divisions']->collects);
        $this->assertNull($resourceArray['divisions']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTheClubIsLoaded(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => ['club'],
            ]
        );

        $resourceArray = (new TeamResource($this->team))->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->team->getId(),
                'name' => 'London Bears',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(ClubResource::class, $resourceArray['club']);
        $this->assertInstanceOf(MissingValue::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['divisions']);
        $this->assertEquals(DivisionResource::class, $resourceArray['divisions']->collects);
        $this->assertNull($resourceArray['divisions']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTheVenueIsLoaded(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => ['venue'],
            ]
        );

        $resourceArray = (new TeamResource($this->team))->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->team->getId(),
                'name' => 'London Bears',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(MissingValue::class, $resourceArray['club']);
        $this->assertInstanceOf(VenueResource::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['divisions']);
        $this->assertEquals(DivisionResource::class, $resourceArray['divisions']->collects);
        $this->assertNull($resourceArray['divisions']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTheDivisionsAreLoaded(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => ['divisions'],
            ]
        );

        $resourceArray = (new TeamResource($this->team))->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->team->getId(),
                'name' => 'London Bears',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(MissingValue::class, $resourceArray['club']);
        $this->assertInstanceOf(MissingValue::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['divisions']);
        $this->assertEquals(DivisionResource::class, $resourceArray['divisions']->collects);
        $this->assertCount(1, $resourceArray['divisions']->collection);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // No need to create roles every time we create a model
        Event::fake();

        $club = Club::factory()->create();
        $venue = Venue::factory()->create();
        $division = Division::factory()->create();
        $this->team = Team::factory()->for($club)->for($venue)->hasAttached($division)->create(
            ['name' => 'London Bears']
        );
    }
}
