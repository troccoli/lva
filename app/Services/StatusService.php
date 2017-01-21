<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 21/01/2017
 * Time: 15:02
 */

namespace App\Services;

use App\Services\Contracts\StatusContract;
use App\Models\UploadJob;

/**
 * Class StatusService
 *
 * This class implements the StatusContract interface using the UploadJobs model.
 * This means that the status will be saved in the DB and that the status codes
 * are the ones defined in the UploadJobs model class.
 *
 * @package App\Services
 */
class StatusService implements StatusContract
{
    /**
     * @param array $status
     *
     * @return array
     */
    public function getNextStepStatus($status)
    {
        $newStatus = [
            'status_code' => UploadJob::STATUS_NOT_STARTED,
        ];

        switch ($status['status_code']) {
            case UploadJob::STATUS_NOT_STARTED:
                $newStatus = [
                    'status_code'     => UploadJob::STATUS_VALIDATING_RECORDS,
                    'processed_lines' => 0,
                ];
                break;
            case UploadJob::STATUS_VALIDATING_RECORDS:
                $newStatus = [
                    'status_code' => UploadJob::STATUS_INSERTING_RECORDS,
                ];
                break;
            case UploadJob::STATUS_INSERTING_RECORDS:
                $newStatus = [
                    'status_code' => UploadJob::STATUS_DONE,
                ];
                break;
        }

        return $newStatus;
    }

    /**
     * @param array $status
     *
     * @return int
     */
    public function getStatusCode($status)
    {
        if (array_has($status, 'status_code')) {
            return $status['status_code'];
        }

        return UploadJob::STATUS_NOT_STARTED;
    }

    /**
     * @param array $status
     *
     * @return int
     * @throws \RuntimeException
     */
    public function getStatusProcessedLines($status)
    {
        $statusCode = $this->getStatusCode($status);

        if ($statusCode == UploadJob::STATUS_VALIDATING_RECORDS && array_has($status, 'processed_lines')) {
            return $status['processed_lines'];
        }

        throw new \RuntimeException('Invalid status for retrieving number of already processed lines.');
    }

    /**
     * @param array $status
     * @param int   $processedLines
     *
     * @throws \RuntimeException
     */
    public function setStatusProcessedLines(&$status, $processedLines)
    {
        $statusCode = $this->getStatusCode($status);

        if ($statusCode == UploadJob::STATUS_VALIDATING_RECORDS) {
            $status['processed_lines'] = $processedLines;
        }

        throw new \RuntimeException('Invalid status for setting number of already processed lines.');
    }

}