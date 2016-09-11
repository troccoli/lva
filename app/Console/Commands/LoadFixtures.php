<?php

namespace App\Console\Commands;

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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $jobId = $this->argument('job');
    }
}
