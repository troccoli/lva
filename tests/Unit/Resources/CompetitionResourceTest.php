<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\CompetitionResource;
use App\Http\Resources\DivisionResource;
use App\Http\Resources\SeasonResource;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use App\Models\User;
use App\Repositories\AccessibleDivisions;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Mockery;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class CompetitionResourceTest extends TestCase
{
    use InteractsWithArrays, WithoutEvents;

    private Competition $competition;
    private AccessibleDivisions $accessibleDivisions;

    protected function setUp(): void
    {
        parent::setUp();

        $season = factory(Season::class)->create();
        $this->competition = factory(Competition::class)->create([
            'name' => 'London Super League',
            'season_id' => $season->getId(),
        ]);
        factory(Division::class)->times(3)->create([
            'competition_id' => $this->competition->getId(),
        ]);

        $this->accessibleDivisions = Mockery::mock(AccessibleDivisions::class);
        $this->app->bind(AccessibleDivisions::class, function () {
            return $this->accessibleDivisions;
        });
    }

    public function testItReturnTheCorrectFields(): void
    {
        $request = Mockery::mock(Request::class, [
            'query' => [],
        ]);

        $resource = new CompetitionResource($this->competition);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id' => $this->competition->getId(),
            'name' => 'London Super League',
        ], $resourceArray);

        $this->assertInstanceOf(MissingValue::class, $resourceArray['season']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['divisions']);
        $this->assertEquals(DivisionResource::class, $resourceArray['divisions']->collects);
        $this->assertNull($resourceArray['divisions']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenSeasonIsLoaded(): void
    {
        $request = Mockery::mock(Request::class, [
            'query' => ['season'],
        ]);

        $resource = new CompetitionResource($this->competition);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id' => $this->competition->getId(),
            'name' => 'London Super League',
        ], $resourceArray);

        $this->assertInstanceOf(SeasonResource::class, $resourceArray['season']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['divisions']);
        $this->assertEquals(DivisionResource::class, $resourceArray['divisions']->collects);
        $this->assertNull($resourceArray['divisions']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenDivisionsAreLoaded(): void
    {
        $request = Mockery::mock(Request::class, [
            'query' => ['divisions'],
            'user' => Mockery::mock(User::class),
        ]);
        $this->accessibleDivisions
            ->shouldReceive('inCompetition')->with($this->competition)->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->competition->getDivisions());

        $resource = new CompetitionResource($this->competition);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent([
            'id' => $this->competition->getId(),
            'name' => 'London Super League',
        ], $resourceArray);

        $this->assertInstanceOf(MissingValue::class, $resourceArray['season']);
        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['divisions']);
        $this->assertEquals(DivisionResource::class, $resourceArray['divisions']->collects);
        $this->assertCount(3, $resourceArray['divisions']->collection);
    }
}
