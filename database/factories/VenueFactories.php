<?php

$factory->define(\LVA\Models\Venue::class, function (\Faker\Generator $faker) {
    return [
        'venue' => $faker->unique()->word,
    ];
});

$factory->define(\LVA\Models\MappedVenue::class, function (\Faker\Generator $faker) {
    return [
        'upload_job_id' => function () {
            return factory(\LVA\Models\UploadJob::class)->create()->id;
        },
        'mapped_venue'  => $faker->unique()->name,
        'venue_id'      => function () {
            return factory(\LVA\Models\Venue::class)->create()->id;
        },
    ];
});

$factory->define(\LVA\Models\VenueSynonym::class, function (\Faker\Generator $faker) {
    return [
        'synonym'  => $faker->unique()->word,
        'venue_id' => function () {
            return factory(\LVA\Models\Venue::class)->create()->id;
        },
    ];
});