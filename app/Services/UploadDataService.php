<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 05/02/2017
 * Time: 17:02.
 */

namespace LVA\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LVA\Models\UploadJobData;

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
            ->setJob($jobId)
            ->setModel($modelClass)
            ->setData(serialize($model))
            ->save();
    }

    /**
     * @param int $jobId
     * @param int $processedRows
     *
     * @return Collection
     */
    public function getUnprocessed($jobId, $processedRows = 0)
    {
        /** @var Collection $rows */
        $rows = UploadJobData::findByJobId($jobId);

        return $rows->slice($processedRows);
    }
}
