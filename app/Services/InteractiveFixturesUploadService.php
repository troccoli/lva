<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 10/09/2016
 * Time: 15:32
 */

namespace LVA\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use LVA\Models\Division;
use LVA\Models\Fixture;
use LVA\Models\Team;
use LVA\Models\UploadJobData;
use LVA\Models\UploadJobStatus;
use LVA\Models\Venue;
use LVA\Repositories\TeamsRepository;
use LVA\Repositories\VenuesRepository;
use LVA\Services\Contracts\InteractiveUploadContract;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use LVA\Models\UploadJob;
use Illuminate\Support\Facades\Validator;

class InteractiveFixturesUploadService implements InteractiveUploadContract
{
    const UPLOAD_DIR = '/app/files/';

    /** @var UploadDataService */
    private $uploadDataService;
    /** @var MappingService */
    private $mappingService;

    /** @var TeamsRepository */
    private $teamsRepository;
    /** @var VenuesRepository */
    private $venuesRepository;

    /**
     * @inheritDoc
     */
    public function __construct(
        UploadDataService $uploadDataService,
        MappingService $mappingService,
        TeamsRepository $teamsRepository,
        VenuesRepository $venuesRepository
    )
    {
        $this->uploadDataService = $uploadDataService;
        $this->mappingService = $mappingService;

        $this->teamsRepository = $teamsRepository;
        $this->venuesRepository = $venuesRepository;
    }

    /**
     * @param resource $handle
     *
     * @return array
     */
    public static function readOneLine(&$handle)
    {
        $line = [];
        foreach (explode(',', str_replace(['"', "\n", "\r"], '', fgets($handle))) as $field) {
            if (is_numeric($field)) {
                $line[] = (int)$field;
            } else {
                $line[] = $field;
            }
        }

        return $line;
    }

    /************************************
     * INTERFACE IMPLEMENTATION METHODS *
     ************************************/

    /**
     * @inheritdoc
     */
    public function createJob($seasonId, UploadedFile $file)
    {
        $job = new UploadJob();
        $job->setSeason($seasonId)
            ->setFile($file->getClientOriginalName())
            ->setType(UploadJob::TYPE_FIXTURES)
            ->setStatus((new UploadJobStatus())->toArray())
            ->save();

        /** @var UploadedFile $fixtureFile */
        $fixtureFile = $file->move(storage_path() . self::UPLOAD_DIR, $job->getId() . '.csv');

        $handle = fopen($fixtureFile->getRealPath(), 'rb');
        $lines = 0;
        while (!feof($handle)) {
            $lines += substr_count(fread($handle, 8192), "\n");
        }
        fclose($handle);

        $job->setRowCount($lines - 1)// Don't count the first line as they are the headers
        ->save();

        return $job;
    }

    /**
     * @inheritDoc
     */
    public function processJob(UploadJob $job)
    {
        /** @var UploadJobStatus $status */
        $status = UploadJobStatus::factory($job->getStatus());

        if ($status->hasNotStarted()) {
            $status->moveForward()->setTotalLines($job->getRowCount());
            $job->setStatus($status->toArray())->save();
        }

        if ($status->isValidating()) {
            $processedLines = $status->getProcessedLines();

            /** @var resource $csvFile */
            $csvFile = fopen(storage_path() . self::UPLOAD_DIR . $job->getId() . '.csv', 'r');

            // Get the headers
            $headers = self::readOneLine($csvFile);

            // Skip already processed lines
            $this->getIntoPosition($csvFile, $processedLines);

            // Start processing
            $allLinesProcessed = true;
            while (!feof($csvFile)) {
                $line = self::readOneLine($csvFile);
                if (empty($line) || count($headers) != count($line)) {
                    continue;
                }
                $row = array_combine($headers, $line);

                $validator = Validator::make($row, [
                    'Code'      => 'required',
                    'Match'     => 'required|integer|min:1',
                    'Home'      => 'required',
                    'Away'      => 'required',
                    'Date'      => 'required|date_format:d/m/Y',
                    'WUTime'    => 'required|date_format:H:i:00',
                    'StartTime' => 'required|date_format:H:i:00',
                    'Hall'      => 'required',
                ]);

                if ($validator->fails()) {
                    $status->setValidationErrors($validator->errors()->all(), $processedLines + 2);
                    $allLinesProcessed = false;
                    break;
                }

                unset($validator);

                // Store the current line
                $status
                    ->setProcessingLineDivision($row['Code'])
                    ->setProcessingLineMatchNumber($row['Match'])
                    ->setProcessingLineHomeTeam($row['Home'])
                    ->setProcessingLineAwayTeam($row['Away'])
                    ->setProcessingLineDate($row['Date'])
                    ->setProcessingLineWarmUpTime($row['WUTime'])
                    ->setProcessingLineStartTime($row['StartTime'])
                    ->setProcessingLineVenue($row['Hall']);

                // Skip lines that are not for London or indoor volleyball
                if ($row['Region'] != 'L' || $row['Discipline'] != 'I') {
                    // Update the line counter
                    $status->setProcessedLines(++$processedLines);
                    $job->setStatus($status->toArray())->save();
                    continue;
                }

                // Skip lines that are for divisions not in the system for the selected season
                $division = Division::findByName($job->getSeason(), $row['Code']);
                if (is_null($division)) {
                    // Update the line counter
                    $status->setProcessedLines(++$processedLines);
                    $job->setStatus($status->toArray())->save();
                    continue;
                }

                /** @var Team|null $homeTeam */
                $homeTeam = $this->teamsRepository->findByName($row['Home']);
                /** @var Team|null $awayTeam */
                $awayTeam = $this->teamsRepository->findByName($row['Away']);
                /** @var Venue|null $venue */
                $venue = $this->venuesRepository->findByName($row['Hall']);

                $isValid = true;

                if (is_null($homeTeam)) {
                    // We can't find an existing team matching this one, so checked the
                    // one mapped during this job
                    $homeTeam = $this->teamsRepository->findByNameWithinMapped($job, $row['Home']);
                    if (is_null($homeTeam)) {
                        // Nope, can't find it, so ask the user what to do
                        $mappings = $this->mappingService->findTeamMappings($division->getId(), $row['Home']);
                        $status->setUnknown(UploadJobStatus::UNKNOWN_HOME_TEAM, $mappings);

                        $allLinesProcessed = false;
                        $isValid = false;
                    }
                }
                if (is_null($awayTeam)) {
                    // We can't find an existing team matching this one, so checked the
                    // one mapped during this job
                    $awayTeam = $this->teamsRepository->findByNameWithinMapped($job, $row['Away']);
                    if (is_null($awayTeam)) {
                        // Nope, can't find it, so ask the user what to do
                        $mappings = $this->mappingService->findTeamMappings($division->getId(), $row['Away']);
                        $status->setUnknown(UploadJobStatus::UNKNOWN_AWAY_TEAM, $mappings);

                        $allLinesProcessed = false;
                        $isValid = false;
                    }
                }
                if (is_null($venue)) {
                    // We can't find an existing venue matching this one, so checked the
                    // one mapped during this job
                    $venue = $this->venuesRepository->findByNameWithinMapped($job, $row['Hall']);
                    if (is_null($venue)) {
                        // Nope, can't find it, so ask the user what to do
                        $mappings = $this->mappingService->findVenueMappings($row['Hall']);
                        $status->setUnknown(UploadJobStatus::UNKNOWN_VENUE, $mappings);

                        $allLinesProcessed = false;
                        $isValid = false;
                    }
                }

                // Is something is not valid stop the process
                if (!$isValid) {
                    break;
                } else {
                    /** @var Fixture $fixture */
                    $fixture = new Fixture();
                    $fixture
                        ->setDivision($division->getId())
                        ->setMatchNumber($row['Match'])
                        ->setMatchDate(Carbon::createFromFormat('d/m/Y', $row['Date']))
                        ->setWarmUpTime(Carbon::createFromFormat('H:i:s', $row['WUTime']))
                        ->setStartTime(Carbon::createFromFormat('H:i:s', $row['StartTime']))
                        ->setHomeTeam($homeTeam->getId())
                        ->setAwayTeam($awayTeam->getId())
                        ->setVenue($venue->getId());

                    $this->uploadDataService->add($job->getId(), Fixture::class, $fixture);

                    unset($fixture);
                }

                // Update the line counter
                $status->setProcessedLines(++$processedLines);
                $job->setStatus($status->toArray())->save();
            }
            fclose($csvFile);

            // If we exited the loop because we have processed all the lines then advance to the next stage
            if ($allLinesProcessed) {
                $status->moveForward()->setTotalRows(UploadJobData::findByJobId($job->getId())->count());
            }

            // Whether we stop because of an error or because we're finished validating, save the status
            $job->setStatus($status->toArray())->save();
        }

        if ($status->isInserting()) {
            /** @var Collection $rows */
            $rows = $this->uploadDataService->getUnprocessed($job->getId());

            // Pass 1:
            // Run all the SQL in a transaction so see if they are all valid
            $valid = true;
            DB::beginTransaction();
            try {
                /** @var UploadJobData $row */
                foreach ($rows as $row) {
                    /** @var Model $model */
                    $model = unserialize($row->model_data);
                    $model->save();
                    unset($model);
                }
            } catch (\Exception $e) {
                $valid = false;
            }
            DB::rollBack();

            // Pass 2:
            // If all SQL is valid run them updating the status and progress
            if (!$valid) {
                $status->setInsertingError($e->getMessage());
                $job->setStatus($status->toArray())->save();
            } else {
                $processedRows = 0;
                /** @var UploadJobData $row */
                foreach ($rows as $row) {
                    /** @var Model $model */
                    $model = unserialize($row->model_data);
                    $model->save();
                    unset($model);

                    // Update the row counter
                    $status->setProcessedRows(++$processedRows);
                    $job->setStatus($status->toArray())->save();
                }

                $status->moveForward();
                $job->setStatus($status->toArray())->save();
            }
        }

        if ($status->isDone()) {
            $this->cleanUp($job);
        }
    }

    /**
     * @param UploadJob $job
     */
    public function cleanUp(UploadJob $job)
    {
        $job->mappedTeams()->delete();
        $job->mappedVenues()->delete();
        $job->uploadData()->delete();
        unlink(storage_path() . self::UPLOAD_DIR . $job->getId() . '.csv');
        if (!UploadJobStatus::factory($job->getStatus())->isDone()) {
            $job->delete();
        }
    }

    /*******************
     * PRIVATE METHODS *
     *******************/

    /**
     * @param resource $file
     * @param int      $numberOfLines
     */
    private function getIntoPosition(&$file, $numberOfLines)
    {
        $counter = 0;
        while ($counter < $numberOfLines && !feof($file)) {
            fgets($file);
            $counter++;
        }
    }
}