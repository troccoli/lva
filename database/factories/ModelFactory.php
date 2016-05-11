<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->unique()->name,
        'email'          => $faker->unique()->email,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(\App\Models\Season::class, function (\Faker\Generator $faker) {
    return [
        'season' => $faker->unique()->word,
    ];
});

$factory->define(App\Models\Division::class, function (\Faker\Generator $faker) {
    return [
        'division'  => $faker->unique()->word,
        'season_id' => function () {
            return factory(App\Models\Season::class)->create()->id;
        },
    ];
});

$factory->define(\App\Models\Club::class, function (\Faker\Generator $faker) {
    return [
        'club' => $faker->unique()->word,
    ];
});

$factory->define(App\Models\Team::class, function (\Faker\Generator $faker) {
    return [
        'team'    => $faker->unique()->word,
        'club_id' => function () {
            return factory(App\Models\Club::class)->create()->id;
        },
    ];
});

$factory->define(\App\Models\Role::class, function (\Faker\Generator $faker) {
    return [
        'role' => $faker->unique()->word,
    ];
});

$factory->define(\App\Models\Venue::class, function (\Faker\Generator $faker) {
    return [
        'venue' => $faker->unique()->word,
    ];
});

$factory->define(\App\Models\Fixture::class, function (\Faker\Generator $faker) {
    return [
        'division_id'  => function () {
            return factory(\App\Models\Division::class)->create()->id;
        },
        'home_team_id' => function () {
            return factory(\App\Models\Team::class)->create()->id;
        },
        'away_team_id' => function () {
            return factory(\App\Models\Team::class)->create()->id;
        },
        'venue_id'     => function () {
            return factory(\App\Models\Venue::class)->create()->id;
        },
        'match_number' => $faker->numberBetween(1, 100),
        'match_date'   => $faker->date('Y-m-d'),
        'warm_up_time' => $faker->time('H:i:s'),
        'start_time'   => $faker->time('H:i:s'),
    ];
});

$factory->define(\App\Models\AvailableAppointment::class, function (\Faker\Generator $faker) {
    return [
        'fixture_id' => function () {
            return factory(\App\Models\Fixture::class)->create()->id;
        },
        'role_id'    => function () {
            return factory(\App\Models\Role::class)->create()->id;
        },
    ];
});