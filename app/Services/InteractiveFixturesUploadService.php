<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 10/09/2016
 * Time: 15:32
 */

namespace LVA\Services;

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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

use LVA\Models\UploadJob;

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
        return explode(',', str_replace(['"', "\n", "\r"], '', fgets($handle)));
    }

    /************************************
     * INTERFACE IMPLEMENTATION METHODS *
     ************************************/

    /**
     * @inheritdoc
     */
    public function createJob($seasonId, UploadedFile $file)
    {
        /** @var UploadedFile $fixtureFile */
        $fixtureFile = $file->move(storage_path() . self::UPLOAD_DIR, $file->getClientOriginalName());

        $handle = fopen($fixtureFile->getRealPath(), 'rb');
        $lines = 0;
        while (!feof($handle)) {
            $lines += substr_count(fread($handle, 8192), "\n");
        }
        fclose($handle);

        $job = new UploadJob();
        $job->setSeason($seasonId)
            ->setFile($fixtureFile->getFilename())
            ->setType(UploadJob::TYPE_FIXTURES)
            ->setRowCount($lines - 1)// Don't count the first line as they are the headers
            ->setStatus((new UploadJobStatus())->toArray())
            ->save();

        return $job;
    }

    /**
     * @inheritDoc
     */
    public function processJob(UploadJob $job)
    {
        /** @var UploadJobStatus $status */
        $status = UploadJobStatus::loadStatus($job->getStatus());

        if ($status->isNotStarted()) {
            $status->moveForward()->setTotalLines($job->getRowCount());
            $job->setStatus($status->toArray())->save();
        }

        if ($status->isValidating()) {
            $processedLines = $status->getProcessedLines();

            /** @var resource $csvFile */
            $csvFile = fopen(storage_path() . self::UPLOAD_DIR . $job->getFile(), 'r');

            // Get the headers
            $headers = self::readOneLine($csvFile);

            // Skip already processed lines
            $this->getIntoPosition($csvFile, $processedLines);

            // Start processing
            $allRowsProcessed = true;
            while (!feof($csvFile)) {
                $row = array_combine($headers, self::readOneLine($csvFile));

                $validator = \Validator::make($row, [
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
                    $status->setErrors($validator->errors()->all());
                    $allRowsProcessed = false;
                    break;
                }

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

                        $allRowsProcessed = false;
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

                        $allRowsProcessed = false;
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

                        $allRowsProcessed = false;
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

            // If we exited the loop because we have processed all line then advance to next stage
            if ($allRowsProcessed) {
                $status->moveForward()->setTotalRows(UploadJobData::findByJobId($job->getId())->count());
            }

            // Whether we stop because of an error or because we're finished validating, save the status
            $job->setStatus($status->toArray())->save();
        }

        if ($status->isInserting()) {
            // @todo insert into DB
        }

        if ($status->isDone()) {
            // @todo Clean up
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

    /**
     * @param UploadJob $job
     * @param array     $data
     */
    private function insertFixture($job, $data)
    {
        /** @var Fixture $fixture */
        $fixture = new Fixture();
        $fixture
            ->setDivision(Division::findByName($job->getSeason(), $data['Code'])->getId())
            ->setMatchNumber($data['Match'])
            ->setMatchDate(Carbon::createFromFormat('d/m/Y', $data['Date']))
            ->setWarmUpTime(Carbon::createFromFormat('H:i:s', $data['WUTime']))
            ->setStartTime(Carbon::createFromFormat('H:i:s', $data['StartTime']))
            ->setHomeTeam(Team::findByName($data['Home'])->getId())
            ->setAwayTeam(Team::findByName($data['Away'])->getId())
            ->setVenue(Venue::findByName($data['Hall'])->getId());

        $this->uploadDataService->add($job->getId(), Fixture::class, $fixture);

        unset($fixture);
    }
}