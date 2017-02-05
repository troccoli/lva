<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 05/02/2017
 * Time: 17:02
 */

namespace App\Services;

use App\Models\UploadJobData;
use Illuminate\Database\Eloquent\Model;

class UploadDataService
{
    /**
     * @param int    $jobId
     * @param string $modelClass
     * @param Model  $model
     */
    public function add($jobId, $modelClass, Model $model)
    {
        $jobData = new UploadJobData();

        $jobData
            ->setJobId($jobId)
            ->setModel($modelClass)
            ->setData($model->toJson())
            ->save();
    }
}