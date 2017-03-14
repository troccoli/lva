<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 13/03/2017
 * Time: 19:01
 */

use League\FactoryMuffin\Faker\Faker;

$fm->define(\LVA\Models\UploadJobStatus::class)->setDefinitions([
    'status_code' => \LVA\Models\UploadJobStatus::STATUS_NOT_STARTED,
]);