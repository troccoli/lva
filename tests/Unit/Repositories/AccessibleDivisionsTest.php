<?php

namespace Tests\Unit\Repositories;

use App\Models\Competition;
use App\Models\Division;
use App\Models\User;
use App\Repositories\AccessibleDivisions;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class AccessibleDivisionsTest extends TestCase
{
    private $sut;
    private $competition;
    private $user;

    public function testItReturnsNoDivisionsIfThereAreNone(): void
    {
        $this->competition
            ->shouldReceive('getDivisions')
            ->andReturn(Collection::make([]));

        $this->assertEmpty($this->sut->inCompetition($this->user, $this->competition));
    }

    public function testItReturnsNoDivisionsWithoutProperPermissions(): void
    {
        $this->competition
            ->shouldReceive('getDivisions')
            ->andReturn(Collection::make([
                Mockery::mock(Division::class, [
                    'getId' => 10,
                ]),
            ]));
        $this->user
            ->shouldReceive('can')
            ->andReturnFalse();

        $this->assertEmpty($this->sut->inCompetition($this->user, $this->competition));
    }

    public function testItReturnsAllDivisionsIfTheUserCanViewDivisionsInCompetition(): void
    {
        $this->competition
            ->shouldReceive('getDivisions')
            ->andReturn(Collection::make([
                Mockery::mock(Division::class, [
                    'getId' => 10,
                ]),
                Mockery::mock(Division::class, [
                    'getId' => 20,
                ]),
            ]));

        $this->user
            ->shouldReceive('can')
            ->with('view-divisions-in-competition-1')
            ->andReturnTrue();

        $data = $this->sut->inCompetition($this->user, $this->competition);
        $this->assertCount(2, $data);
        $this->assertSame(10, $data[0]->getId());
        $this->assertSame(20, $data[1]->getId());
    }

    public function testItReturnsOnlyTheDivisionsForWhichTheUserCanViewTheFixtures(): void
    {
        $this->competition
            ->shouldReceive('getDivisions')
            ->andReturn(Collection::make([
                Mockery::mock(Division::class, [
                    'getId' => 10,
                ]),
                Mockery::mock(Division::class, [
                    'getId' => 20,
                ]),
            ]));

        $this->user
            ->shouldReceive('can')
            ->with('view-divisions-in-competition-1')
            ->andReturnFalse()
            ->shouldReceive('can')
            ->with('view-fixtures-in-division-10')
            ->andReturnTrue()
            ->shouldReceive('can')
            ->with('view-fixtures-in-division-20')
            ->andReturnFalse();

        $data = $this->sut->inCompetition($this->user, $this->competition);
        $this->assertCount(1, $data);
        $this->assertSame(10, $data[0]->getId());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->sut = new AccessibleDivisions();
        $this->competition = Mockery::mock(Competition::class, [
            'getId' => 1,
        ]);
        $this->user = Mockery::mock(User::class);
    }
}
