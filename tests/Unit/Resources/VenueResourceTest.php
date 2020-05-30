<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\ClubResource;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class VenueResourceTest extends TestCase
{
    use InteractsWithArrays;

    private $venue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->venue = factory(Venue::class)->create(['name' => 'The Box']);

        aClub()->withVenue($this->venue)->build();
        aClub()->withVenue($this->venue)->build();
    }

    public function testItReturnTheCorrectFields(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => [],
        ]);

        $resource = new VenueResource($this->venue);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'   => $this->venue->getId(),
            'name' => 'The Box',
        ], $resourceArray);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['clubs']);
        $this->assertEquals(ClubResource::class, $resourceArray['clubs']->collects);
        $this->assertNull($resourceArray['clubs']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTheClubsAreLoaded(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => ['clubs'],
        ]);

        $resource = new VenueResource($this->venue);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'   => $this->venue->getId(),
            'name' => 'The Box',
        ], $resourceArray);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['clubs']);
        $this->assertEquals(ClubResource::class, $resourceArray['clubs']->collects);
        $this->assertCount(2, $resourceArray['clubs']->collection);
    }
}
