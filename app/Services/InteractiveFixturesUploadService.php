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
use LVA\Models\Season;
use LVA\Models\Team;
use LVA\Models\TeamSynonym;
use LVA\Models\UploadJobData;
use LVA\Models\UploadJobStatus;
use LVA\Models\Venue;
use LVA\Models\VenueSynonym;
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

    /** @var StatusService */
    private $statusService;
    /** @var UploadDataService */
    private $uploadDataService;
    /** @var MappingService */
    private $mappingService;

    /** @var TeamsRepository */
    private $teamsRepository;
    /** @var VenuesRepository */
    private $venuesRepository;


    /** @var Collection */
    private $mappedTeams;
    /** @var Collection */
    private $mappedVenues;
    /** @var Collection */
    private $newVenues;

    /**
     * @inheritDoc
     */
    public function __construct(
        StatusService $statusService,
        UploadDataService $uploadDataService,
        MappingService $mappingService,
        TeamsRepository $teamsRepository,
        VenuesRepository $venuesRepository
    ) {
        $this->statusService = $statusService;
        $this->uploadDataService = $uploadDataService;
        $this->mappingService = $mappingService;

        $this->teamsRepository = $teamsRepository;
        $this->venuesRepository = $venuesRepository;
    }


    /************************************
     * INTERFACE IMPLEMENTATION METHODS *
     ************************************/

    /**
     * @param resource $handle
     *
     * @return array
     */
    public static function readOneLine(&$handle)
    {
        return array_map(function ($field) {
            return str_replace(['"', "\n", "\r"], '', $field);
        }, explode(',', fgets($handle)));
    }

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
            ->setStatus($this->statusService->getInitialStatus()->toArray())
            ->save();

        return $job;
    }

    /*******************
     * PRIVATE METHODS *
     *******************/

    /**
     * @inheritDoc
     */
    public function processJob(UploadJob $job)
    {
        $this->mappedTeams = $job->mappedTeams;
        $this->mappedVenues = $job->mappedVenues;
        $this->newVenues = $job->newVenues;

        /** @var UploadJobStatus $status */
        $status = $this->statusService->loadStatus($job->getStatus());

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

                // Store the current line
                $this->statusService->setProcessingLine($status,
                    $row['Code'],
                    $row['Match'],
                    $row['Home'],
                    $row['Away'],
                    Carbon::createFromFormat('d/m/Y', $row['Date']),
                    Carbon::createFromFormat('H:i:s', $row['WUTime']),
                    Carbon::createFromFormat('H:i:s', $row['StartTime']),
                    $row['Hall']
                );

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
                        $this->statusService->setUnknownHomeTeam($status, $mappings);

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
                        $this->statusService->setUnknownAwayTeam($status, $mappings);

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
                        $this->statusService->setUnknownHomeTeam($status, $mappings);

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

    /**
     * @param resource $file
     * @param int      $numberOfLines
     */
    private function getIntoPosition($file, $numberOfLines)
    {
        $counter = 0;
        while ($counter < $numberOfLines && !feof($file)) {
            fgets($file);
            $counter++;
        }
    }

    /**
     * @param string $team
     *
     * @return bool
     */
    private function isValidTeam($team)
    {
        /** @var Team $model */
        $model = Team::findByName($team);
        if (is_null($model)) {
            $model = TeamSynonym::findBySynonym($team);
        }
        if (is_null($model)) {
            if (!in_array($team, $this->mappedTeams->pluck('team')->all())) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $venue
     *
     * @return bool
     */
    private function isValidVenue($venue)
    {
        /** @var Venue $model */
        $model = Venue::findByName($venue);
        if (is_null($model)) {
            $model = VenueSynonym::findBySynonym($venue);
        }
        if (is_null($model)) {
            if (!in_array($venue, $this->mappedVenues->pluck('venue')->all()) &&
                !in_array($venue, $this->newVenues->pluck('venue')->all())
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $team
     *
     * @return Team|null
     */
    private function findTeamByName($team)
    {
        /** @var Team $model */
        $model = Team::findByName($team);
        if ($model) {
            return $model;
        }

        /** @var TeamSynonym $modelSynonym */
        $modelSynonym = TeamSynonym::findBySynonym($team);
        if ($modelSynonym) {
            $model = $modelSynonym->team;
            return $model;
        }

        return null;
    }

    /**
     * @param string $venue
     *
     * @return Venue|null
     */
    private function findVenueByName($venue)
    {
        /** @var Venue $model */
        $model = Venue::findByName($venue);
        if ($model) {
            return $model;
        }

        /** @var VenueSynonym $modelSynonym */
        $modelSynonym = VenueSynonym::findBySynonym($venue);
        if ($modelSynonym) {
            $model = $modelSynonym->venue;
            return $model;
        }

        return null;
    }

    /**
     * @param UploadJob   $job
     * @param array $data
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