<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 26/09/2016
 * Time: 18:33
 */

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
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

    public function mapTeam()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response($uploadJob);
        }

        // @todo implement mapping a team

        return response()->json([
            'success' => true,
        ]);
    }

    public function addVenue()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response($uploadJob);
        }

        // @todo implement adding a venue

        return response()->json([
            'success' => true,
        ]);
    }

    public function mapVenue()
    {
        $uploadJob = $this->checkForJob();
        if (!$uploadJob instanceof UploadJob) {
            return response($uploadJob);
        }

        // @todo implemenr mapping a venue

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