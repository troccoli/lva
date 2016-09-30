<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 10/09/2016
 * Time: 15:32
 */

namespace App\Services;

use App\Services\Contracts\InteractiveUploadContract;
use Illuminate\Http\UploadedFile;

use App\Models\UploadJob;

class InteractiveFixturesUploadService implements InteractiveUploadContract
{
    const UPLOAD_DIR = '/app/files/';

    /*******************
     * PRIVATE METHODS *
     *******************/

    /**
     * @inheritdoc
     */
    public function createJob(UploadedFile $file)
    {
        $fixtureFile = $file->move(storage_path() . self::UPLOAD_DIR, $file->getClientOriginalName());

        $job = UploadJob::create([
            'file'   => $fixtureFile->getFilename(),
            'type'   => UploadJob::TYPE_FIXTURES,
            'status' => ['status_code' => UploadJob::STATUS_NOT_STARTED],
        ]);

        return $job->id;
    }

    /**
     * @inheritDoc
     */
    public function processJob(UploadJob $job)
    {
        $statusCode = $this->getStatusCode($job);

        if ($statusCode == UploadJob::STATUS_NOT_STARTED) {
            $status = $this->initialStatus();
            $this->updateStatus($job, $status);
        }

        if ($statusCode == UploadJob::STATUS_VALIDATING_RECORDS) {
            //$this->validateFixtures();
        }

        if ($statusCode == UploadJob::STATUS_INSERTING_RECORDS) {
            // insert into DB
        }

        if ($statusCode == UploadJob::STATUS_DONE) {
            // Clean up
        }
    }

    /************************************
     * INTERFACE IMPLEMENTATION METHODS *
     ************************************/

    /**
     * @param UploadJob $job
     * @return int
     */
    private function getStatusCode(UploadJob $job)
    {
        $status = $job->status;

        if (array_has($status, 'status_code')) {
            return $status['status_code'];
        }

        return UploadJob::STATUS_NOT_STARTED;
    }

    /**
     * @return array
     */
    private function initialStatus()
    {
        return [
            'status_code' => UploadJob::STATUS_VALIDATING_RECORDS,
        ];
    }

    /**
     * @inheritDoc
     */
    public function updateStatus(UploadJob $job, array $newStatus)
    {
        $job->status = $newStatus;
        $job->save();
    }


}