<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Club;
use Faker\Generator as Faker;

$factory->define(Club::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->company,
    ];
});
