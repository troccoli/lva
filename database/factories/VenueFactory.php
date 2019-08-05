<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Venue;
use Faker\Generator as Faker;

$factory->define(Venue::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->company,
    ];
});
