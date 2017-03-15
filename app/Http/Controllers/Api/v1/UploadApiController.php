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
use LVA\Models\TeamSynonym;
use LVA\Models\UploadJob;
use LVA\Models\UploadJobStatus;
use LVA\Models\VenueSynonym;
use LVA\Services\InteractiveFixturesUploadService;
use Illuminate\Support\Facades\Input;
use LVA\Services\UploadDataService;

class UploadApiController extends Controller
{
    /** @var InteractiveFixturesUploadService */
    private $uploadService;
    /** @var UploadDataService */
    private $uploadDataService;

    /**
     * @inheritDoc
     */
    public function __construct(InteractiveFixturesUploadService $uploadService, UploadDataService $uploadDataService)
    {
        $this->uploadService = $uploadService;
        $this->uploadDataService = $uploadDataService;
    }

    public function resumeUpload()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response()->json($uploadJob);
        }

        /** @var UploadJobStatus $status */
        $status = UploadJobStatus::factory($uploadJob->getStatus());

        if ($status->canResume()) {
            $status->resume();

            $uploadJob->setStatus($status->toArray())->save();

            $this->uploadService->processJob($uploadJob);
        }
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

    public function mapTeam(MapTeamRequest $request)
    {
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