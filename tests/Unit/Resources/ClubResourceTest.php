<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\ClubResource;
use App\Http\Resources\TeamResource;
use App\Http\Resources\VenueResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Tests\TestCase;
use Tests\AssertArrayContent;

class ClubResourceTest extends TestCase
{
    use RefreshDatabase, AssertArrayContent;

    private $club;

    protected function setUp(): void
    {
        parent::setUp();

        $this->club = aClub()->withName('Phoenix Robins')->build();
        aTeam()->inClub($this->club)->build(6);
    }

    public function testItReturnTheCorrectFields(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => [],
        ]);

        $resource = new ClubResource($this->club);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'   => $this->club->getId(),
            'name' => 'Phoenix Robins',
        ], $resourceArray);

        $this->assertInstanceOf(MissingValue::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['teams']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertNull($resourceArray['teams']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTheVenueIsLoaded(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => ['venue'],
        ]);

        $resource = new ClubResource($this->club);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'   => $this->club->getId(),
            'name' => 'Phoenix Robins',
        ], $resourceArray);

        $this->assertInstanceOf(VenueResource::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['teams']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertNull($resourceArray['teams']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTheTeamsAreLoaded(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => ['teams'],
        ]);

        $resource = new ClubResource($this->club);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'   => $this->club->getId(),
            'name' => 'Phoenix Robins',
        ], $resourceArray);

        $this->assertInstanceOf(MissingValue::class, $resourceArray['venue']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['teams']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertCount(6, $resourceArray['teams']->collection);
    }
}
