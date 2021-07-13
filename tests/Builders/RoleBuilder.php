<?php

namespace Tests\Builders;

use Faker\Factory;
use Spatie\Permission\Models\Role;

class RoleBuilder
{
    private array $overrides = [];

    public function __construct()
    {
        $faker = Factory::create();
        $this->overrides = [
            'name' => $faker->name(),
        ];
    }

    public function named(string $name): self
    {
        $this->overrides['name'] = $name;

        return $this;
    }

    public function build(): Role
    {
        return Role::create($this->overrides);
    }
}
