<?php

namespace App\Events;

use App\Models\Competition;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CompetitionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Competition $competition) {}
}
