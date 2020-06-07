<?php

namespace Tests\Builders;

use Spatie\Permission\Models\Role;

class RoleBuilder
{
    private array $overrides = [];

    public function named(string $name): self
    {
        $this->overrides['name'] = $name;

        return $this;
    }

    public function build(): Role
    {
        return factory(Role::class)->create($this->overrides);
    }
}
