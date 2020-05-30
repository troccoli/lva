<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\CompetitionResource;
use App\Http\Resources\DivisionResource;
use App\Http\Resources\TeamResource;
use App\Models\Competition;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class DivisionResourceTest extends TestCase
{
    use RefreshDatabase, InteractsWithArrays;

    private $division;

    protected function setUp(): void
    {
        parent::setUp();

        $competition = factory(Competition::class)->create();
        $this->division = factory(Division::class)->create([
            'name'           => 'DIV1BM',
            'display_order'  => 3,
            'competition_id' => $competition->getId(),
        ]);
        aTeam()->inDivision($this->division)->build(4);
    }

    public function testItReturnTheCorrectFields(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => [],
        ]);

        $resource = new DivisionResource($this->division);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'            => $this->division->getId(),
            'name'          => 'DIV1BM',
            'display_order' => 3,
        ], $resourceArray);

        $this->assertInstanceOf(MissingValue::class, $resourceArray['competition']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertNull($resourceArray['teams']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenCompetitionIsLoaded(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => ['competition'],
        ]);

        $resource = new DivisionResource($this->division);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'            => $this->division->getId(),
            'name'          => 'DIV1BM',
            'display_order' => 3,
        ], $resourceArray);

        $this->assertInstanceOf(CompetitionResource::class, $resourceArray['competition']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertNull($resourceArray['teams']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTeamsAreLoaded(): void
    {
        $request = \Mockery::mock(Request::class, [
            'query' => ['teams'],
        ]);

        $resource = new DivisionResource($this->division);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id'            => $this->division->getId(),
            'name'          => 'DIV1BM',
            'display_order' => 3,
        ], $resourceArray);

        $this->assertInstanceOf(MissingValue::class, $resourceArray['competition']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertCount(4, $resourceArray['teams']->collection);
    }
}
