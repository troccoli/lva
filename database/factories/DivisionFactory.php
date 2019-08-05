<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Competition;
use App\Models\Division;
use Faker\Generator as Faker;

$factory->define(Division::class, function (Faker $faker) {
    return [
        'competition_id' => function () {
            return factory(Competition::class)->create()->id;
        },
        'name' => $faker->unique()->company,
        'display_order' => $faker->unique()->numberBetween(1)
    ];
});
