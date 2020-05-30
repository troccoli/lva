<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\CompetitionResource;
use App\Http\Resources\SeasonResource;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class SeasonResourceTest extends TestCase
{
    use RefreshDatabase, InteractsWithArrays;

    private $season;

    protected function setUp(): void
    {
        parent::setUp();

        $this->season = factory(Season::class)->create(['year' => 2014]);
        factory(Competition::class)->create([
            'season_id' => $this->season->getId(),
        ]);
    }

    public function testItReturnTheCorrectFields(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => [],
        ]);

        $resource = new SeasonResource($this->season);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'   => $this->season->getId(),
            'name' => '2014/15',
        ], $resourceArray);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['competitions']);
        $this->assertEquals(CompetitionResource::class, $resourceArray['competitions']->collects);
        $this->assertNull($resourceArray['competitions']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenCompetitionsAreLoaded(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => ['competitions'],
        ]);

        $resource = new SeasonResource($this->season);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'   => $this->season->getId(),
            'name' => '2014/15',
        ], $resourceArray);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['competitions']);
        $this->assertEquals(CompetitionResource::class, $resourceArray['competitions']->collects);
        $this->assertCount(1, $resourceArray['competitions']->collection);
    }
}
