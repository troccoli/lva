<?php

namespace App\Events;

use App\Models\Division;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DivisionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Division $division;

    public function __construct(Division $division)
    {
        $this->division = $division;
    }
}
