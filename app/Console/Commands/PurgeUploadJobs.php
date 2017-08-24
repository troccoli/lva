<?php

namespace LVA\Console\Commands;

use Illuminate\Console\Command;
use LVA\Models\UploadJob;
use LVA\Services\InteractiveFixturesUploadService;

class PurgeUploadJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lva:purge-upload-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command delete all jobs older than one week.';

    /** @var InteractiveFixturesUploadService */
    private $uploadService;

    /**
     * PurgeUploadJobs constructor.
     *
     * @param InteractiveFixturesUploadService $uploadService
     */
    public function __construct(InteractiveFixturesUploadService $uploadService)
    {
        parent::__construct();
        $this->uploadService = $uploadService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $jobs = UploadJob::stale()->get();

        foreach ($jobs as $job) {
            $this->uploadService->cleanUp($job);
        }
    }
}
