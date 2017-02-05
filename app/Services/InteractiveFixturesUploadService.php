<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 10/09/2016
 * Time: 15:32
 */

namespace App\Services;

use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\TeamSynonym;
use App\Models\UploadJobData;
use App\Models\UploadJobStatus;
use App\Models\Venue;
use App\Models\VenueSynonym;
use App\Services\Contracts\InteractiveUploadContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;

use App\Models\UploadJob;

class InteractiveFixturesUploadService implements InteractiveUploadContract
{
    const UPLOAD_DIR = '/app/files/';

    /** @var StatusService */
    private $statusService;
    private $uploadDataService;

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
        UploadDataService $uploadDataService
    )
    {
        $this->statusService = $statusService;
        $this->uploadDataService = $uploadDataService;
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
    public function createJob(UploadedFile $file)
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
        $job->setFile($fixtureFile->getFilename())
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

                // Start validation
                $isValid = true;
                if (!$this->isValidTeam($row['Home'])) {
                    $allRowsProcessed = false;
                    $isValid = false;
                    $this->statusService->setUnknownHomeTeam($status);
                }
                if (!$this->isValidTeam($row['Away'])) {
                    $allRowsProcessed = false;
                    $isValid = false;
                    $this->statusService->setUnknownHomeTeam($status);
                }
                if (!$this->isValidVenue($row['Hall'])) {
                    $allRowsProcessed = false;
                    $isValid = false;
                    $this->statusService->setUnknownVenue($status);
                }

                // Is something is not valid stop the process
                if (!$isValid) {
                    break;
                }

                // Everything is ok, so store the fixture to be inserted later
                $this->insertFixture($job->getId(), $row);

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
     * @param int   $jobId
     * @param array $data
     */
    private function insertFixture($jobId, $data)
    {
        /** @var Fixture $fixture */
        $fixture = new Fixture();
        $fixture
            ->setDivision(Division::findByName($data['Code'])->getId())
            ->setMatchNumber($data['Match'])
            ->setMatchDate(Carbon::createFromFormat('d/m/Y', $data['Date']))
            ->setWarmUpTime(Carbon::createFromFormat('H:i:s', $data['WUTime']))
            ->setStartTime(Carbon::createFromFormat('H:i:s', $data['StartTime']))
            ->setHomeTeam(Team::findByName($data['Home'])->getId())
            ->setAwayTeam(Team::findByName($data['Away'])->getId())
            ->setVenue(Venue::findByName($data['Hall'])->getId());

        $this->uploadDataService->add($jobId, Fixture::class, $fixture);

        unset($fixture);
    }
}