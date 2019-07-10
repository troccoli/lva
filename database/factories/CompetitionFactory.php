<?php

/** @var Factory $factory */

use App\Models\Competition;
use App\Models\Season;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Competition::class, function (Faker $faker) {
    return [
        'season_id' => function () {
            return factory(Season::class)->create()->id;
        },
        'name' => $faker->unique()->company,
    ];
});
