<?php

namespace App\Events;

use App\Models\Division;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DivisionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Division $division) {}
}
