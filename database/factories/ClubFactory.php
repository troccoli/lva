<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Club;
use App\Models\Venue;
use Faker\Generator as Faker;

$factory->define(Club::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->state,
        'venue_id' => function () {
            return factory(Venue::class)->create()->getId();
        }
    ];
});
