<?php

namespace App\Events;

use App\Models\Season;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeasonCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Season $season;

    public function __construct(Season $season)
    {
        $this->season = $season;
    }
}
