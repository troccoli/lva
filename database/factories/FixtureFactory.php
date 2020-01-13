<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Faker\Generator as Faker;

$factory->define(Fixture::class, function (Faker $faker) {
    return [
        'match_number' => $faker->unique()->randomNumber(),
        'division_id' => function () {
            return factory(Division::class)->create()->getId();
        },
        'home_team_id' => function () {
            return factory(Team::class)->create()->getId();
        },
        'away_team_id' =>  function () {
            return factory(Team::class)->create()->getId();
        },
        'match_date' => $faker->date(),
        'match_time' => $faker->time(),
        'venue_id' => function () {
            return factory(Venue::class)->create()->getId();
        }
    ];
});
