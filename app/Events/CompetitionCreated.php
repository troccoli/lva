<?php

namespace App\Events;

use App\Models\Competition;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompetitionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Competition $competition;

    public function __construct(Competition $competition)
    {
        $this->competition = $competition;
    }
}
