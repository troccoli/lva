<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\CompetitionResource;
use App\Http\Resources\DivisionResource;
use App\Http\Resources\TeamResource;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Team;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class DivisionResourceTest extends TestCase
{
    use InteractsWithArrays, WithoutEvents;

    private Division $division;

    public function testItReturnTheCorrectFields(): void
    {
        $request = Mockery::mock(
            Request::class,
            [
                'query' => [],
            ]
        );

        $resource = new DivisionResource($this->division);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->division->getId(),
                'name' => 'DIV1BM',
                'display_order' => 3,
            ],
            $resourceArray
        );

        $this->assertInstanceOf(MissingValue::class, $resourceArray['competition']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertNull($resourceArray['teams']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenCompetitionIsLoaded(): void
    {
        $request = Mockery::mock(
            Request::class,
            [
                'query' => ['competition'],
            ]
        );

        $resource = new DivisionResource($this->division);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->division->getId(),
                'name' => 'DIV1BM',
                'display_order' => 3,
            ],
            $resourceArray
        );

        $this->assertInstanceOf(CompetitionResource::class, $resourceArray['competition']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertNull($resourceArray['teams']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenTeamsAreLoaded(): void
    {
        $request = Mockery::mock(
            Request::class,
            [
                'query' => ['teams'],
            ]
        );

        $resource = new DivisionResource($this->division);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->division->getId(),
                'name' => 'DIV1BM',
                'display_order' => 3,
            ],
            $resourceArray
        );

        $this->assertInstanceOf(MissingValue::class, $resourceArray['competition']);
        $this->assertEquals(TeamResource::class, $resourceArray['teams']->collects);
        $this->assertCount(4, $resourceArray['teams']->collection);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // No need to create roles every time we create a model
        Event::fake();

        /** @var Competition $competition */
        $competition = Competition::factory()->create();
        $this->division = Division::factory()->create(
            [
                'name' => 'DIV1BM',
                'display_order' => 3,
                'competition_id' => $competition->getId(),
            ]
        );
        Team::factory()->hasAttached($this->division)->count(4)->create();
    }
}
