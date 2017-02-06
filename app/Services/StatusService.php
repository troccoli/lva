<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 21/01/2017
 * Time: 15:02
 */

namespace App\Services;

use App\Models\UploadJobStatus;
use Carbon\Carbon;

/**
 * Class StatusService
 *
 * This class implements the StatusContract interface using the UploadJobs model.
 * This means that the status will be saved in the DB and that the status codes
 * are the ones defined in the UploadJobs model class.
 *
 * @package App\Services
 */
class StatusService
{
    /** @var MappingService */
    private $mappingService;

    /**
     * StatusService constructor.
     *
     * @param MappingService $mappingService
     */
    public function __construct(MappingService $mappingService)
    {
        $this->mappingService = $mappingService;
    }


    /**
     * @param array $statusArray
     *
     * @return UploadJobStatus
     */
    public function loadStatus(array $statusArray)
    {
        $status = new UploadJobStatus();
        $status->load($statusArray);

        return $status;
    }

    /**
     * @return UploadJobStatus
     */
    public function getInitialStatus()
    {
        return new UploadJobStatus();
    }

    /**
     * @param UploadJobStatus $status
     * @param string          $division
     * @param string          $matchNumber
     * @param string          $homeTeam
     * @param string          $awayTeam
     * @param Carbon          $date
     * @param Carbon          $warmUpTime
     * @param Carbon          $startTime
     * @param string          $venue
     */
    public function setProcessingLine(
        UploadJobStatus $status,
        $division,
        $matchNumber,
        $homeTeam,
        $awayTeam,
        Carbon $date,
        Carbon $warmUpTime,
        Carbon $startTime,
        $venue
    ) {
        $status
            ->setProcessingLineDivision($division)
            ->setProcessingLineMatchNumber($matchNumber)
            ->setProcessingLineHomeTeam($homeTeam)
            ->setProcessingLineAwayTeam($awayTeam)
            ->setProcessingLineDate($date->format('D d/m/y'))
            ->setProcessingLineWarmUpTime($warmUpTime->format('H:i'))
            ->setProcessingLineStartTime($startTime->format('H:i'))
            ->setProcessingLineVenue($venue);
    }

    /**
     * @param array $statusArray
     *
     * @return array
     */
    public function apiFormat($statusArray)
    {
        $status = new UploadJobStatus();
        $status->load($statusArray);

        $formattedStatus = [
            'StatusCode'    => $status->getStatusCode(),
            'StatusMessage' => $status->getStatusCodeMessage(),
        ];

        if ($status->isValidating()) {
            $formattedStatus['Progress'] = floor($status->getProcessedLines() * 100 / $status->getTotalLines());
        } elseif ($status->isInserting()) {
            $formattedStatus['Progress'] = floor($status->getProcessedRows() * 100 / $status->getTotalRows());
        }

        $formattedStatus['Fixture'] = [
            'Division'    => $status->getProcessingLineDivision(),
            'MatchNumber' => $status->getProcessingLineMatchNumber(),
            'HomeTeam'    => $status->getProcessingLineHomeTeam(),
            'AwayTeam'    => $status->getProcessingLineAwayTeam(),
            'Date'        => $status->getProcessingLineDate(),
            'WarmUpTime'  => $status->getProcessingLineWarmUpTime(),
            'StartTime'   => $status->getProcessingLineStartTime(),
            'Venue'       => $status->getProcessingLineVenue(),
        ];

        foreach ($status->getUnknowns() as $unknownType => $mappings) {
            switch ($unknownType) {
                case UploadJobStatus::UNKNOWN_HOME_TEAM:
                    $formattedStatus['Unknowns']['HomeTeam'] = [
                        'Mapping' => $mappings,
                        'ApiUrls' => [
                            'Map' => route('loading-map-team'),
                        ],
                    ];
                    break;
                case UploadJobStatus::UNKNOWN_AWAY_TEAM:
                    $formattedStatus['Unknowns']['AwayTeam'] = [
                        'Mapping' => $mappings,
                        'ApiUrls' => [
                            'Map' => route('loading-map-team'),
                        ],
                    ];
                    break;
                case UploadJobStatus::UNKNOWN_VENUE:
                    $formattedStatus['Unknowns']['Venue'] = [
                        'Mapping' => $mappings,
                        'ApiUrls' => [
                            'Add' => route('loading-add-venue'),
                            'Map' => route('loading-map-venue'),
                        ],
                    ];
                    break;
            }
        }

        return $formattedStatus;
    }

    /**
     * @param UploadJobStatus $status
     *
     * @return UploadJobStatus
     */
    public function getNextStepStatus($status)
    {
        return $status->moveForward();
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

        return UploadJobStatus::STATUS_NOT_STARTED;
    }

    /**
     * @param UploadJobStatus $status
     * @param array           $mappings
     */
    public function setUnknownHomeTeam(UploadJobStatus $status, $mappings)
    {
        $status->setUnknown(UploadJobStatus::UNKNOWN_HOME_TEAM, $mappings);
    }

    /**
     * @param UploadJobStatus $status
     * @param array           $mappings
     */
    public function setUnknownAwayTeam(UploadJobStatus $status, $mappings)
    {
        $status->setUnknown(UploadJobStatus::UNKNOWN_AWAY_TEAM, $mappings);
    }

    /**
     * @param UploadJobStatus $status
     * @param array           $mappings
     */
    public function setUnknownVenue(UploadJobStatus $status, $mappings)
    {
        $status->setUnknown(UploadJobStatus::UNKNOWN_VENUE, $mappings);
    }
}