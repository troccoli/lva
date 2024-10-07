<?php

namespace App\Events;

use App\Models\Season;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeasonCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Season $season) {}
}
