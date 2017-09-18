<?php

$factory->define(LVA\Models\Team::class, function (\Faker\Generator $faker) {
    return [
        'team'    => $faker->unique()->word,
        'club_id' => function () {
            return factory(\LVA\Models\Club::class)->create()->id;
        },
        'trigram' => $faker->unique()->regexify('[A-Z0-9]{3}'),
    ];
});

$factory->define(\LVA\Models\MappedTeam::class, function (\Faker\Generator $faker) {
    return [
        'upload_job_id' => function () {
            return factory(\LVA\Models\UploadJob::class)->create()->id;
        },
        'mapped_team'   => $faker->unique()->name,
        'team_id'       => function () {
            return factory(\LVA\Models\Team::class)->create()->id;
        },
    ];
});

$factory->define(\LVA\Models\TeamSynonym::class, function (\Faker\Generator $faker) {
    return [
        'synonym' => $faker->unique()->word,
        'team_id' => function () {
            return factory(\LVA\Models\Team::class)->create()->id;
        },
    ];
});
