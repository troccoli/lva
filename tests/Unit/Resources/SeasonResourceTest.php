<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\CompetitionResource;
use App\Http\Resources\SeasonResource;
use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use App\Repositories\AccessibleCompetitions;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Event;
use Tests\Concerns\InteractsWithArrays;
use Tests\TestCase;

class SeasonResourceTest extends TestCase
{
    use InteractsWithArrays, WithoutEvents;

    private Season $season;
    private AccessibleCompetitions $accessibleCompetitions;

    public function testItReturnTheCorrectFields(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => [],
            ]
        );

        $resource = new SeasonResource($this->season);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->season->getId(),
                'name' => '2014/15',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['competitions']);
        $this->assertEquals(CompetitionResource::class, $resourceArray['competitions']->collects);
        $this->assertNull($resourceArray['competitions']->collection);
    }

    public function testItReturnTheCorrectFieldsWhenCompetitionsAreLoaded(): void
    {
        $request = \Mockery::mock(
            Request::class,
            [
                'query' => ['competitions'],
                'user' => \Mockery::mock(User::class),
            ]
        );
        $this->accessibleCompetitions
            ->shouldReceive('inSeason')->andReturnSelf()
            ->shouldReceive('get')->andReturn($this->season->getCompetitions());

        $resource = new SeasonResource($this->season);
        $resourceArray = $resource->toArray($request);

        $this->assertArrayContent(
            [
                'id' => $this->season->getId(),
                'name' => '2014/15',
            ],
            $resourceArray
        );

        $this->assertInstanceOf(AnonymousResourceCollection::class, $resourceArray['competitions']);
        $this->assertEquals(CompetitionResource::class, $resourceArray['competitions']->collects);
        $this->assertCount(1, $resourceArray['competitions']->collection);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // No need to create roles every time we create a model
        Event::fake();

        $this->season = Season::factory()->create(['year' => 2014]);
        Competition::factory()->create(
            [
                'season_id' => $this->season->getId(),
            ]
        );

        $this->accessibleCompetitions = \Mockery::mock(AccessibleCompetitions::class);

        $this->app->bind(
            AccessibleCompetitions::class,
            function () {
                return $this->accessibleCompetitions;
            }
        );
    }
}
