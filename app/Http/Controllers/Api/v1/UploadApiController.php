<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 26/09/2016
 * Time: 18:33
 */

namespace LVA\Http\Controllers\Api\v1;

use LVA\Http\Controllers\Controller;
use LVA\Http\Requests\Api\v1\MapTeamRequest;
use LVA\Http\Requests\Api\v1\MapVenueRequest;
use LVA\Http\Requests\Api\v1\NewVenueRequest;
use LVA\Models\MappedTeam;
use LVA\Models\MappedVenue;
use LVA\Models\NewVenue;
use LVA\Models\UploadJob;
use LVA\Models\UploadJobStatus;
use LVA\Services\InteractiveFixturesUploadService;
use Illuminate\Support\Facades\Input;

class UploadApiController extends Controller
{
    /** @var InteractiveFixturesUploadService */
    private $uploadService;

    /**
     * @inheritDoc
     */
    public function __construct(InteractiveFixturesUploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function resumeUpload()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response()->json($uploadJob);
        }

        $status = new UploadJobStatus();
        $status->load($uploadJob->getStatus())->resume();

        $uploadJob->setStatus($status->toArray())->save();

        $this->uploadService->processJob($uploadJob);
    }

    public function getUploadStatus()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response()->json($uploadJob);
        }

        $status = new UploadJobStatus();
        $status->load($uploadJob->getStatus());

        return response()->json([
            'Timestamp' => time(),
            'Error'     => false,
            'Message'   => 'Job found',
            'Status'    => $status->toApiJson(),
        ]);
    }

    public function mapTeam(MapTeamRequest $request)
    {
        $mappedTeam = new MappedTeam();
        $mappedTeam
            ->setUploadJob($request->input('job'))
            ->setName($request->input('name'))
            ->setMappedTeam($request->input('newName'))
            ->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function addVenue(NewVenueRequest $request)
    {
        $newVenue = new NewVenue();
        $newVenue
            ->setUploadJob($request->input('job'))
            ->setName($request->input('name'))
            ->save();

        return response()->json([
            'success' => true,
        ]);
    }

    public function mapVenue(MapVenueRequest $request)
    {
        $mappedVenue = new MappedVenue();
        $mappedVenue
            ->setUploadJob($request->input('job'))
            ->setName($request->input('name'))
            ->setMappedVenue($request->input('newName'))
            ->save();

        return response()->json([
            'success' => false,
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