<?php

/** @var Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Spatie\Permission\Models\Role;

$factory->define(Role::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->jobTitle,
        'guard_name' => 'web',
    ];
});
