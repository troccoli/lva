<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 26/09/2016
 * Time: 18:33
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\MapTeamRequest;
use App\Http\Requests\Api\v1\MapVenueRequest;
use App\Http\Requests\Api\v1\NewVenueRequest;
use App\Models\MappedTeam;
use App\Models\MappedVenue;
use App\Models\NewVenue;
use App\Models\UploadJob;
use App\Models\UploadJobStatus;
use App\Services\StatusService;
use Illuminate\Support\Facades\Input;

class UploadApiController extends Controller
{
    /** @var StatusService */
    private $statusService;

    /**
     * @inheritDoc
     */
    public function __construct(StatusService $statusService)
    {
        $this->statusService = $statusService;
    }


    public function getUploadStatus()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response()->json($uploadJob);
        }

        $status = new UploadJobStatus();
        $status->load([
            'status_code' => UploadJobStatus::STATUS_UNKNOWN_DATA,
        ]);
        $f = [
            'StatusCode'    => $status->getStatusCode(),
            'StatusMessage' => $status->getStatusCodeMessage(),
            'Progress'      => rand(10, 99),
            'Fixture'       => [
                'Division'    => 'M1A',
                'MatchNumber' => '05',
                'HomeTeam'    => 'K.S. Osmeka Men 2',
                'AwayTeam'    => 'Flaming Six Blackjacks',
                'Date'        => date('D d/m/y'),
                'WarmUpTime'  => '15:00',
                'StartTime'   => '15:20',
                'Venue'       => 'Battersea Sports Centre',
            ],
            'Unknowns'      => [
                'HomeTeam' => [
                    'Mapping' => [
                        ['value' => 1, 'text' => 'KS Osemka Men 2'],
                        ['value' => 3, 'text' => 'KS Osemka Men 3'],
                        ['value' => 4, 'text' => 'KS Osemka Men 4'],
                        ['value' => 2, 'text' => 'k.s. Osemka Men 2'],
                    ],
                    'ApiUrls' => [
                        'Map' => '/api/v1/maps/team',
                    ],
                ],
                'AwayTeam' => [
                    'Mapping' => [
                        ['value' => 1, 'text' => 'KS Osemka Men 2'],
                        ['value' => 3, 'text' => 'KS Osemka Men 3'],
                        ['value' => 4, 'text' => 'KS Osemka Men 4'],
                        ['value' => 2, 'text' => 'k.s. Osemka Men 2'],
                    ],
                    'ApiUrls' => [
                        'Map' => '/api/v1/maps/team',
                    ],
                ],
                'Venue'    => [
                    'Mapping' => [
                        ['value' => 10, 'text' => 'Sobell S.C.'],
                        ['value' => 22, 'text' => 'SportsDock'],
                    ],
                    'ApiUrls' => [
                        'Add' => route('loading-add-venue'),
                        'Map' => route('loading-map-venue'),
                    ],
                ],

            ],
        ];

        return response()->json([
            'Timestamp' => time(),
            'Error'     => false,
            'Message'   => 'Job found',
            'Status'    => $f,
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