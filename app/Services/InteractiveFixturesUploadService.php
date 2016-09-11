<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli <giulio@troccoli.it>
 * Date: 10/09/2016
 * Time: 15:32
 */

namespace App\Services;

use Illuminate\Http\UploadedFile;

use App\Models\UploadJob;
use App\Services\Interfaces\InteractiveUploadInterface;
use Symfony\Component\HttpFoundation\File\File;

class InteractiveFixturesUploadService implements InteractiveUploadInterface
{
    const UPLOAD_DIR = '/app/files/';

    /** @var StatusLoggerService */
    private $logger;

    /** @var \SplFileObject */
    private $statusFile;

    /**
     * @inheritDoc
     */
    public function __construct(StatusLoggerService $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function createJob(UploadedFile $file)
    {
        $fixtureFile = $file->move(storage_path() . self::UPLOAD_DIR);

        $job = UploadJob::create([
            'file'   => $fixtureFile->getFilename(),
            'type'   => UploadJob::TYPE_FIXTURES,
            'status' => json_encode([]),
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
            // Start
        }

        if ($statusCode == UploadJob::STATUS_VALIDATING_RECORDS) {
            // validate each record
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

    /*******************
     * PRIVATE METHODS *
     *******************/

    private function getStatusCode(UploadJob $job)
    {
        $status = $job->status;

        if (array_has($status, 'status_code')) {
            return $status['status_code'];
        }

        return UploadJob::STATUS_NOT_STARTED;
    }

    private function initialStatus()
    {
        return [

        ];
    }

    /**
     * @inheritDoc
     */
    public function updateStatus(UploadJob $job, array $newStatus)
    {
        if (is_null($this->statusFile)) {
            $this->statusFile = $this->logger->createStatusFile('upload_fixtures_status_' . $job->id);
        }

        $this->logger->setStatus($this->statusFile, json_encode($newStatus));
    }


}