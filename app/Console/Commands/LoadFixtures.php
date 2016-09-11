<?php

namespace App\Console\Commands;

use App\Models\UploadJob;
use App\Services\InteractiveFixturesUploadService;
use Illuminate\Console\Command;

class LoadFixtures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lva:load:fixtures {job : The id of the job to run}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload of fixtures from a CSV file';
    /** @var InteractiveFixturesUploadService */
    private $service;

    /**
     * LoadFixtures constructor.
     * @param InteractiveFixturesUploadService $service
     */
    public function __construct(InteractiveFixturesUploadService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $jobId = $this->argument('job');

        $job = UploadJob::findOrFail($jobId);

        $this->service->processJob($job);

        // Get the status and find out last stage of the process

        // Skip already processed stages

        // Run current and all next stages
    }
}
