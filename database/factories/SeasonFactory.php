<?php

/** @var Factory $factory */

use App\Models\Season;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Carbon;

$factory->define(Season::class, function (Faker $faker) {
    return [
        'year' => $faker->unique()->year,
    ];
});
$factory->state(Season::class, 'last-year', function (Faker $faker) {
    return [
        'created_at' => Carbon::now()->subYear(),
    ];
});
