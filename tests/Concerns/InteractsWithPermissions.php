<?php

namespace Tests\Concerns;

use App\Models\User;

trait InteractsWithPermissions
{
    public function assertUserCan(User $user, string $permission): self
    {
        $this->assertTrue($user->can($permission));

        return $this;
    }

    public function assertUserCannot(User $user, string $permission): self
    {
        $this->assertTrue($user->cannot($permission));

        return $this;
    }

    public function assertUserCant(User $user, string $permission): self
    {
        return $this->assertUserCannot($user, $permission);
    }
}
