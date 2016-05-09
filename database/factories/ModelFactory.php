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
        'name'           => $faker->name,
        'email'          => $faker->email,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(\App\Models\Season::class, function (\Faker\Generator $faker) {
    return [
        'season' => $faker->word,
    ];
});

$factory->define(App\Models\Division::class, function (\Faker\Generator $faker) {
    return [
        'division'  => $faker->word,
        'season_id' => function () {
            return factory(App\Models\Season::class)->create()->id;
        },
    ];
});

$factory->define(\App\Models\Club::class, function (\Faker\Generator $faker) {
    return [
        'club' => $faker->word,
    ];
});

$factory->define(App\Models\Team::class, function (\Faker\Generator $faker) {
    return [
        'team'    => $faker->word,
        'club_id' => function () {
            return factory(App\Models\Club::class)->create()->id;
        },
    ];
});