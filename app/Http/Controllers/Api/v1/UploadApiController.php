<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 26/09/2016
 * Time: 18:33.
 */

namespace LVA\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use LVA\Http\Controllers\Controller;
use LVA\Models\MappedTeam;
use LVA\Models\MappedVenue;
use LVA\Models\TeamSynonym;
use LVA\Models\UploadJob;
use LVA\Models\UploadJobStatus;
use LVA\Models\VenueSynonym;
use LVA\Services\InteractiveFixturesUploadService;
use LVA\Services\UploadDataService;

class UploadApiController extends Controller
{
    /** @var InteractiveFixturesUploadService */
    private $uploadService;
    /** @var UploadDataService */
    private $uploadDataService;

    /**
     * {@inheritdoc}
     */
    public function __construct(InteractiveFixturesUploadService $uploadService, UploadDataService $uploadDataService)
    {
        $this->uploadService = $uploadService;
        $this->uploadDataService = $uploadDataService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function resumeUpload()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response()->json($uploadJob);
        }

        /** @var UploadJobStatus $status */
        $status = UploadJobStatus::factory($uploadJob->getStatus());

        if (!$status->canResume()) {
            return response()->json([
                'Timestamp' => time(),
                'Error'     => true,
                'Message'   => 'Job cannot be resumed',
            ]);
        }

        $status->resume();

        $uploadJob->setStatus($status->toArray())->save();

        $this->uploadService->processJob($uploadJob);

        return response()->json([
            'Timestamp' => time(),
            'Error'     => false,
            'Message'   => 'Job resumed',
        ]);
    }

    public function abandonUpload()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response()->json($uploadJob);
        }

        /** @var UploadJobStatus $status */
        $status = UploadJobStatus::factory($uploadJob->getStatus());

        if (!$status->isWaitingConfirmation()) {
            return response()->json([
                'Timestamp' => time(),
                'Error'     => true,
                'Message'   => 'Job cannot be abandoned',
            ]);
        }

        $this->uploadService->cleanUp($uploadJob);

        return response()->json([
            'Timestamp' => time(),
            'Error'     => false,
            'Message'   => 'Job abandoned',
        ]);
    }

    public function getUploadStatus()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response()->json($uploadJob);
        }

        /** @var UploadJobStatus $status */
        $status = UploadJobStatus::factory($uploadJob->getStatus());

        return response()->json([
            'Timestamp' => time(),
            'Error'     => false,
            'Message'   => 'Job found',
            'Status'    => $status->toApiArray(),
        ]);
    }

    public function mapTeam(Request $request)
    {
        $this->validate($request, [
            'job'     => 'required|exists:upload_jobs,id',
            'name'    => 'required',
            'newName' => 'required',
        ]);

        $mappedTeam = new MappedTeam();
        $mappedTeam
            ->setUploadJob($request->input('job'))
            ->setName($request->input('name'))
            ->setMappedTeam($request->input('newName'))
            ->save();

        $synonym = new TeamSynonym();
        $synonym
            ->setSynonym($mappedTeam->getName())
            ->setTeam($request->input('newName'));

        $this->uploadDataService->add($request->input('job'), TeamSynonym::class, $synonym);

        return response()->json([
            'success' => true,
        ]);
    }

    public function mapVenue(Request $request)
    {
        $this->validate($request, [
            'job'     => 'required|exists:upload_jobs,id',
            'name'    => 'required',
            'newName' => 'required',
        ]);

        $mappedVenue = new MappedVenue();
        $mappedVenue
            ->setUploadJob($request->input('job'))
            ->setName($request->input('name'))
            ->setMappedVenue($request->input('newName'))
            ->save();

        $synonym = new VenueSynonym();
        $synonym
            ->setSynonym($mappedVenue->getName())
            ->setVenue($request->input('newName'));

        $this->uploadDataService->add($request->input('job'), VenueSynonym::class, $synonym);

        return response()->json([
            'success' => true,
        ]);
    }

    private function checkForJob()
    {
        $jobId = Input::get('job', null);
        if (null === $jobId) {
            return [
                'Timestamp' => time(),
                'Error'     => true,
                'Message'   => 'Job parameter missing',
            ];
        }
        $uploadJob = UploadJob::find($jobId);

        if (is_null($uploadJob)) {
            return [
                'Timestamp' => time(),
                'Error'     => true,
                'Message'   => 'Job not found',
            ];
        }

        return $uploadJob;
    }
}
