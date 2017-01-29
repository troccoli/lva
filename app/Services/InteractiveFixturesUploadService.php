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

    /** @var Collection */
    private $mappedTeams;
    /** @var Collection */
    private $mappedVenues;
    /** @var Collection */
    private $newTeams;
    /** @var Collection */
    private $newVenues;

    /**
     * @inheritDoc
     */
    public function __construct(StatusService $statusService)
    {
        $this->statusService = $statusService;
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
            ->setStatus(['status_code' => UploadJob::STATUS_NOT_STARTED])
            ->save();

        return $job->getId();
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
        $this->newTeams = $job->newTeams;
        $this->newVenues = $job->newVenues;

        $status = $job->getStatus();

        $statusCode = $this->statusService->getStatusCode($status);

        if ($statusCode == UploadJob::STATUS_NOT_STARTED) {

            $status = $this->statusService->getNextStepStatus($status);
            $statusCode = $this->statusService->getStatusCode($status);
            $job->setStatus($status)->save();
        }

        if ($statusCode == UploadJob::STATUS_VALIDATING_RECORDS) {
            $processedLines = $this->statusService->getStatusProcessedLines($status);

            /** @var resource $csvFile */
            $csvFile = fopen(storage_path() . self::UPLOAD_DIR . $job->getFile(), 'r');

            $headers = self::readOneLine($csvFile);

            $this->getIntoPosition($csvFile, $processedLines);

            $allRowsProcessed = true;
            while (!feof($csvFile)) {
                $row = array_combine($headers, self::readOneLine($csvFile));
                if (!$this->isValidRow($row)) {
                    // @todo Prepare for asking the user what to do
                    $allRowsProcessed = false;
                    break;
                }
                $this->insertFixture($job->getId(), $row);

                $processedLines++;
                $this->statusService->setStatusProcessedLines($status, $processedLines);
                $job->setStatus($status)->save();
            }
            fclose($csvFile);

            if ($allRowsProcessed) {
                $status = $this->statusService->getNextStepStatus($status);
                $statusCode = $this->statusService->getStatusCode($status);
                $job->setStatus($status)->save();
            }
        }

        if ($statusCode == UploadJob::STATUS_INSERTING_RECORDS) {
            // @todo insert into DB
        }

        if ($statusCode == UploadJob::STATUS_DONE) {
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
     * @param array $row
     *
     * @return bool
     */
    private function isValidRow($row)
    {
        $isValid = true;

        $isValid = $isValid && $this->isValidTeam($row['Home']);
        $isValid = $isValid && $this->isValidTeam($row['Away']);

        $isValid = $isValid && $this->isValidVenue($row['Hall']);

        return $isValid;
    }

    private function isValidTeam($team)
    {
        $model = Team::findByName($team);
        if (is_null($model)) {
            $model = TeamSynonym::findBySynonym($team);
        }
        if (is_null($model)) {
            if (!in_array($team, $this->mappedTeams->pluck('team')->all()) &&
                !in_array($team, $this->newTeams->pluck('team')->all())
            ) {
                return false;
            }
        }

        return true;
    }

    private function isValidVenue($venue)
    {
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

    private function insertFixture($jobId, $data)
    {

        $fixture = new Fixture();
        $fixture
            ->setDivision(Division::find($data['Code'])->getId())
            ->setMatchNumber($data['Match'])
            ->setMatchDate(Carbon::createFromFormat('d/m/Y', $data['date']))
            ->setWarmUpTime(Carbon::createFromFormat('H:i:s', $data['WUTime']))
            ->setStartTime(Carbon::createFromFormat('H:i:s', $data['StartTime']))
            ->setHomeTeam(Team::findByName($data['Home'])->getId())
            ->setAwayTeam(Team::findByName($data['Away'])->getId())
            ->setVenue(Venue::findByName($data['Hall'])->getId());

        $jobData = new UploadJobData();

        $jobData
            ->setJobId($jobId)
            ->setModel(Fixture::class)
            ->setData($fixture->toJson())
            ->save();

        unset($fixture);
    }
}