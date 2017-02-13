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

$factory->define(LVA\User::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->unique()->name,
        'email'          => $faker->unique()->email,
        'password'       => bcrypt($faker->unique()->password()),
        'remember_token' => $faker->unique()->md5,
    ];
});

$factory->define(\LVA\Models\Season::class, function (\Faker\Generator $faker) {
    return [
        'season' => $faker->unique()->word,
    ];
});

$factory->define(LVA\Models\Division::class, function (\Faker\Generator $faker) {
    return [
        'division'  => $faker->unique()->word,
        'season_id' => function () {
            return factory(LVA\Models\Season::class)->create()->id;
        },
    ];
});

$factory->define(\LVA\Models\Club::class, function (\Faker\Generator $faker) {
    return [
        'club' => $faker->unique()->word,
    ];
});

$factory->define(LVA\Models\Team::class, function (\Faker\Generator $faker) {
    return [
        'team'    => $faker->unique()->word,
        'club_id' => function () {
            return factory(LVA\Models\Club::class)->create()->id;
        },
    ];
});

$factory->define(\LVA\Models\Role::class, function (\Faker\Generator $faker) {
    return [
        'role' => $faker->unique()->word,
    ];
});

$factory->define(\LVA\Models\Venue::class, function (\Faker\Generator $faker) {
    return [
        'venue' => $faker->unique()->word,
    ];
});

$factory->define(\LVA\Models\Fixture::class, function (\Faker\Generator $faker) {
    return [
        'division_id'  => function () {
            return factory(\LVA\Models\Division::class)->create()->id;
        },
        'home_team_id' => function () {
            return factory(\LVA\Models\Team::class)->create()->id;
        },
        'away_team_id' => function () {
            return factory(\LVA\Models\Team::class)->create()->id;
        },
        'venue_id'     => function () {
            return factory(\LVA\Models\Venue::class)->create()->id;
        },
        'match_number' => $faker->unique()->numberBetween(1, 100),
        'match_date'   => $faker->unique()->date('Y-m-d'),
        'warm_up_time' => $faker->unique()->time('H:i:s'),
        'start_time'   => $faker->unique()->time('H:i:s'),
    ];
});

$factory->define(\LVA\Models\AvailableAppointment::class, function (\Faker\Generator $faker) {
    return [
        'fixture_id' => function () {
            return factory(\LVA\Models\Fixture::class)->create()->id;
        },
        'role_id'    => function () {
            return factory(\LVA\Models\Role::class)->create()->id;
        },
    ];
});