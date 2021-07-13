<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\ClubResource;
use App\Http\Resources\TeamResource;
use App\Http\Resources\VenueResource;
use App\Models\Club;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Event;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class ClubResourceTest extends TestCase
{
    use InteractsWithArrays;

    private $club;

    public function testItReturnTheCorrectFields(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => [],
            ]
        );

        $resource = new ClubResource($this->club);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->club->getId(),
                'name' => 'Phoenix Robins',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(MissingValue::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['teams']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertNull($resourceArray['teams']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTheVenueIsLoaded(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => ['venue'],
            ]
        );

        $resource = new ClubResource($this->club);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->club->getId(),
                'name' => 'Phoenix Robins',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(VenueResource::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['teams']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertNull($resourceArray['teams']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTheTeamsAreLoaded(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => ['teams'],
            ]
        );

        $resource = new ClubResource($this->club);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->club->getId(),
                'name' => 'Phoenix Robins',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(MissingValue::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['teams']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertCount(6, $resourceArray['teams']->collection);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // No need to create roles every time we create a model
        Event::fake();

        $this->club = Club::factory()->create(['name' => 'Phoenix Robins']);
        Team::factory()->for($this->club)->count(6)->create();
    }
}
