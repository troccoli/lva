<?php

/** @var Factory $factory */

use App\Models\Competition;
use App\Models\Season;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Competition::class, function (Faker $faker) {
    static $divNumber = 0;

    $gender = ['M', 'W'][mt_rand(0, 1)];
    $name = $divNumber === 0
        ? $gender . 'P'
        : 'DIV' . $divNumber . $gender;
    $divNumber++;
    return [
        'season_id' => function () {
            return factory(Season::class)->create()->id;
        },
        'name' => $name
    ];
});
