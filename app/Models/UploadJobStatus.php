<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 05/02/2017
 * Time: 12:13
 */

namespace LVA\Models;

use Carbon\Carbon;


/**
 * Class UploadJobStatus
 *
 * NOTE: This model does not extend the Eloquent Model class because it is not
 *       to be store in the DB. It's just a convenient way to encapsulate all
 *       the data and functionality needed for the status
 *
 * @package LVA\Models
 */
class UploadJobStatus
{
    const STATUS_VALIDATING_RECORDS = 1;
    const STATUS_INSERTING_RECORDS = 2;
    const STATUS_NOT_STARTED = 0;
    const STATUS_UNKNOWN_DATA = 10;
    const STATUS_DONE = 99;

    const UNKNOWN_HOME_TEAM = 1;
    const UNKNOWN_AWAY_TEAM = 2;
    const UNKNOWN_VENUE = 3;

    private $status_code;
    private $total_lines;
    private $processed_lines;
    private $total_rows;
    private $processed_rows;
    private $processing_line;
    private $unknowns;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->status_code = self::STATUS_NOT_STARTED;
        $this->total_lines = 0;
        $this->processed_lines = 0;
        $this->total_rows = 0;
        $this->processed_rows = 0;
        $this->processing_line = [];
        $this->unknowns = null;
    }

    public static function loadStatus($statusArray)
    {
        $status = new static();
        $status->load($statusArray);

        return $status;
    }

    /**
     * @return string
     */
    public function getStatusCodeMessage()
    {
        switch ($this->status_code) {
            case self::STATUS_NOT_STARTED:
                return 'Not started';
            case self::STATUS_VALIDATING_RECORDS:
                return 'Validating records';
            case self::STATUS_INSERTING_RECORDS:
                return 'Inserting records';
            case self::STATUS_UNKNOWN_DATA:
                return 'Unknown data';
            case self::STATUS_DONE:
                return 'Done';
            default:
                return "Status code {$this->status_code} not recognised";
        }
    }

    /**
     * @param array $data
     *
     * @return UploadJobStatus
     */
    public function load(array $data)
    {
        $this->status_code = $data['status_code'];

        if (array_has($data, 'total_lines')) {
            $this->total_lines = $data['total_lines'];
        }
        if (array_has($data, 'processed_lines')) {
            $this->processed_lines = $data['processed_lines'];
        }
        if (array_has($data, 'total_rows')) {
            $this->total_rows = $data['total_rows'];
        }
        if (array_has($data, 'processed_rows')) {
            $this->processed_rows = $data['processed_rows'];
        }
        if (array_has($data, 'processing_line')) {
            $this->processing_line = $data['processing_line'];
        }
        if (array_has($data, 'unknowns')) {
            $this->unknowns = $data['unknowns'];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'status_code'     => $this->getStatusCode(),
            'total_lines'     => $this->getTotalLines(),
            'processed_lines' => $this->getProcessedLines(),
            'total_rows'      => $this->getTotalRows(),
            'processed_rows'  => $this->getProcessedRows(),
            'processing_line' => $this->processing_line,
            'unknowns'        => $this->getUnknowns(),
        ];
    }

    /**
     * @return array
     */
    public function toApiJson()
    {
        $formattedStatus = [
            'StatusCode'    => $this->getStatusCode(),
            'StatusMessage' => $this->getStatusCodeMessage(),
        ];

        if ($this->isValidating()) {
            $formattedStatus['Progress'] = floor($this->getProcessedLines() * 100 / $this->getTotalLines());
        } elseif ($this->isInserting()) {
            $formattedStatus['Progress'] = floor($this->getProcessedRows() * 100 / $this->getTotalRows());
        }

        $formattedStatus['Fixture'] = [
            'Division'    => $this->getProcessingLineDivision(),
            'MatchNumber' => $this->getProcessingLineMatchNumber(),
            'HomeTeam'    => $this->getProcessingLineHomeTeam(),
            'AwayTeam'    => $this->getProcessingLineAwayTeam(),
            'Date'        => $this->getProcessingLineDate(),
            'WarmUpTime'  => $this->getProcessingLineWarmUpTime(),
            'StartTime'   => $this->getProcessingLineStartTime(),
            'Venue'       => $this->getProcessingLineVenue(),
        ];

        foreach ($this->getUnknowns() as $unknownType => $mappings) {
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
                            //'Add' => route('loading-add-venue'),
                            'Map' => route('loading-map-venue'),
                        ],
                    ];
                    break;
            }
        }

        return $formattedStatus;
    }

    /**
     * @return bool
     */
    public function isNotStarted()
    {
        return $this->status_code === self::STATUS_NOT_STARTED;
    }

    /**
     * @return bool
     */
    public function isValidating()
    {
        return $this->status_code === self::STATUS_VALIDATING_RECORDS;
    }

    /**
     * @return bool
     */
    public function isInserting()
    {
        return $this->status_code === self::STATUS_INSERTING_RECORDS;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return $this->status_code === self::STATUS_DONE;
    }

    /**
     * @return bool
     */
    public function hasUnknownData()
    {
        return $this->status_code === self::STATUS_UNKNOWN_DATA;
    }

    /**
     * @return array
     */
    public function getUnknowns()
    {
        if (empty($this->unknowns)) {
            return [];
        }

        return $this->unknowns;
    }

    /**
     * @param int   $unknownType
     * @param array $mappings
     */
    public function setUnknown($unknownType, $mappings)
    {
        $this->status_code = self::STATUS_UNKNOWN_DATA;
        $this->unknowns[$unknownType] = $mappings;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @return int
     */
    public function getTotalLines()
    {
        return $this->total_lines;
    }

    /**
     * @param int $total_lines
     *
     * @return UploadJobStatus
     */
    public function setTotalLines($total_lines)
    {
        $this->total_lines = $total_lines;

        return $this;
    }

    /**
     * @return int
     */
    public function getProcessedLines()
    {
        return $this->processed_lines;
    }

    /**
     * @param int $processed_lines
     *
     * @return UploadJobStatus
     */
    public function setProcessedLines($processed_lines)
    {
        $this->processed_lines = $processed_lines;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalRows()
    {
        return $this->total_rows;
    }

    /**
     * @param int $total_rows
     *
     * @return UploadJobStatus
     */
    public function setTotalRows($total_rows)
    {
        $this->total_rows = $total_rows;

        return $this;
    }

    /**
     * @return int
     */
    public function getProcessedRows()
    {
        return $this->processed_rows;
    }

    /**
     * @param int $processed_rows
     *
     * @return UploadJobStatus
     */
    public function setProcessedRows($processed_rows)
    {
        $this->processed_rows = $processed_rows;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingLineDivision()
    {
        return $this->processing_line['division'];

    }

    /**
     * @param string $division
     *
     * @return UploadJobStatus
     */
    public function setProcessingLineDivision($division)
    {
        $this->processing_line['division'] = $division;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingLineMatchNumber()
    {
        return $this->processing_line['match_number'];

    }

    /**
     * @param string $matchNumber
     *
     * @return UploadJobStatus
     */
    public function setProcessingLineMatchNumber($matchNumber)
    {
        $this->processing_line['match_number'] = $matchNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingLineHomeTeam()
    {
        return $this->processing_line['home_team'];

    }

    /**
     * @param string $team
     *
     * @return UploadJobStatus
     */
    public function setProcessingLineHomeTeam($team)
    {
        $this->processing_line['home_team'] = $team;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingLineAwayTeam()
    {
        return $this->processing_line['away_team'];

    }

    /**
     * @param string $team
     *
     * @return UploadJobStatus
     */
    public function setProcessingLineAwayTeam($team)
    {
        $this->processing_line['away_team'] = $team;

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingLineDate()
    {
        return $this->processing_line['date'];

    }

    /**
     * @param string $date
     *
     * @return UploadJobStatus
     */
    public function setProcessingLineDate($date)
    {
        $this->processing_line['date'] = Carbon::createFromFormat('d/m/Y', $date)->format('D, d/m/Y');

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingLineWarmUpTime()
    {
        return $this->processing_line['warm_up_time'];

    }

    /**
     * @param string $time
     *
     * @return UploadJobStatus
     */
    public function setProcessingLineWarmUpTime($time)
    {
        $this->processing_line['warm_up_time'] = Carbon::createFromFormat('H:i:s', $time)->format('H:i');

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingLineStartTime()
    {
        return $this->processing_line['start_time'];

    }

    /**
     * @param string $time
     *
     * @return UploadJobStatus
     */
    public function setProcessingLineStartTime($time)
    {
        $this->processing_line['start_time'] = Carbon::createFromFormat('H:i:s', $time)->format('H:i');

        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingLineVenue()
    {
        return $this->processing_line['venue'];

    }

    /**
     * @param string $venue
     *
     * @return UploadJobStatus
     */
    public function setProcessingLineVenue($venue)
    {
        $this->processing_line['venue'] = $venue;

        return $this;
    }

    /**
     * @return UploadJobStatus
     */
    public function moveForward()
    {
        switch ($this->status_code) {
            case self::STATUS_NOT_STARTED:
                $this->status_code = self::STATUS_VALIDATING_RECORDS;
                break;
            case self::STATUS_VALIDATING_RECORDS:
                $this->status_code = self::STATUS_INSERTING_RECORDS;
                break;
            case self::STATUS_INSERTING_RECORDS:
                $this->status_code = self::STATUS_DONE;
                break;
            case self::STATUS_DONE:
                break;
            default:
                throw new \RuntimeException("Invalid status code {$this->status_code}.");
        }

        return $this;
    }

    /**
     * @return UploadJobStatus
     */
    public function resume()
    {
        if ($this->status_code === self::STATUS_UNKNOWN_DATA) {
            $this->status_code = self::STATUS_VALIDATING_RECORDS;
            $this->unknowns = null;
        }

        return $this;
    }
}