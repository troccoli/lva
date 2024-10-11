<?php

namespace App\Events;

use App\Models\Club;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClubCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Club $club) {}
}
