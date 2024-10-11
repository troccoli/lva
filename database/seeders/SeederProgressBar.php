<?php

namespace Database\Seeders;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

trait SeederProgressBar
{
    private ProgressBar $progressBar;

    private function initProgressBar(?int $max = null): void
    {
        $this->progressBar = new ProgressBar(new ConsoleOutput);
        $this->progressBar->setFormat('[%bar%] %percent:3s%%');
        $this->progressBar->start($max);
    }

    private function advanceProgressBar(int $step = 1): void
    {
        $this->progressBar->advance($step);
    }

    private function finishProgressBar(): void
    {
        $this->progressBar->finish();
        echo "\n";
    }
}
