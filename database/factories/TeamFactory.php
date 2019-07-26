<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Club;
use App\Models\Team;
use Faker\Generator as Faker;

$factory->define(Team::class, function (Faker $faker) {
    return [
        'club_id' => function () {
            return factory(Club::class)->create()->id;
        },
        'name' => $faker->unique()->company,
    ];
});
