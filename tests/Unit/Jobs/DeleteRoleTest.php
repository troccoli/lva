<?php

namespace Tests\Unit\Jobs;

use App\Jobs\DeleteRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DeleteRoleTest extends TestCase
{
    public function testItRemovesTheRoleForAllUsersAndDeletesIt(): void
    {
        $user = \Mockery::mock(User::class)
                        ->shouldReceive('removeRole')->once()->andReturnTrue()
                        ->getMock();

        \Mockery::mock(User::class)->shouldNotHaveReceived('getAttribute');

        $role = \Mockery::mock(Role::class)
                        ->shouldReceive('getAttribute')->with('users')->andReturn(EloquentCollection::make([$user]))
                        ->shouldReceive('delete')->once()->andReturnTrue()
                        ->getMock();

        $sut = new DeleteRole(Collection::make([$role]));

        $sut->handle();
    }
}
