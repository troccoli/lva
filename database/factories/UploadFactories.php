<?php

$factory->define(\LVA\Models\UploadJob::class, function (\Faker\Generator $faker) {
    return [
        'file'      => $faker->word.str_random(5).'.csv',
        'type'      => 'fixtures',
        'status'    => json_encode(['status_code' => \LVA\Models\UploadJobStatus::STATUS_NOT_STARTED]),
        'season_id' => function () {
            return factory(\LVA\Models\Season::class)->create()->id;
        },
        'row_count' => $faker->numberBetween(1, 100),
    ];
});

$factory->define(\LVA\Models\UploadJobData::class, function (\Faker\Generator $faker) {
    return [
        'upload_job_id' => function () {
            return factory(\LVA\Models\UploadJob::class)->create()->id;
        },
        'model'         => \LVA\Models\Fixture::class,
        'model_data'    => serialize(factory(\LVA\Models\Fixture::class)->make()),
    ];
});

$factory->defineAs(\LVA\Models\UploadJobData::class, \LVA\Models\TeamSynonym::class,
    function (\Faker\Generator $faker) {
        return [
            'upload_job_id' => function () {
                return factory(\LVA\Models\UploadJob::class)->create()->id;
            },
            'model'         => \LVA\Models\TeamSynonym::class,
            'model_data'    => serialize(factory(\LVA\Models\TeamSynonym::class)->make()),
        ];
    });

$factory->defineAs(\LVA\Models\UploadJobData::class, \LVA\Models\VenueSynonym::class,
    function (\Faker\Generator $faker) {
        return [
            'upload_job_id' => function () {
                return factory(\LVA\Models\UploadJob::class)->create()->id;
            },
            'model'         => \LVA\Models\VenueSynonym::class,
            'model_data'    => serialize(factory(\LVA\Models\VenueSynonym::class)->make()),
        ];
    });

$factory->defineAs(\LVA\Models\UploadJobData::class, \LVA\Models\Fixture::class, function (\Faker\Generator $faker) {
    return [
        'upload_job_id' => function () {
            return factory(\LVA\Models\UploadJob::class)->create()->id;
        },
        'model'         => \LVA\Models\Fixture::class,
        'model_data'    => serialize(factory(\LVA\Models\Fixture::class)->make()),
    ];
});
