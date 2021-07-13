<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\ClubResource;
use App\Http\Resources\VenueResource;
use App\Models\Club;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Event;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class VenueResourceTest extends TestCase
{
    use InteractsWithArrays;

    private $venue;

    public function testItReturnTheCorrectFields(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => [],
            ]
        );

        $resource = new VenueResource($this->venue);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->venue->getId(),
                'name' => 'The Box',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['clubs']);
        $this->assertEquals(ClubResource::class, $resourceArray['clubs']->collects);
        $this->assertNull($resourceArray['clubs']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTheClubsAreLoaded(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => ['clubs'],
            ]
        );

        $resource = new VenueResource($this->venue);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->venue->getId(),
                'name' => 'The Box',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['clubs']);
        $this->assertEquals(ClubResource::class, $resourceArray['clubs']->collects);
        $this->assertCount(2, $resourceArray['clubs']->collection);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // No need to create roles every time we create a model
        Event::fake();

        $this->venue = Venue::factory()->create(['name' => 'The Box']);

        Club::factory()->count(2)->for($this->venue)->create();
    }
}
