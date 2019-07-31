<?php

namespace Tests\Builders;

use App\Models\Club;
use App\Models\Venue;

class ClubBuilder
{
    private $data = [];

    public function withName(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function withVenue(Venue $venue): self
    {
        $this->data['venue_id'] = $venue->getId();

        return $this;
    }

    public function build(): Club
    {
        return factory(Club::class)->create($this->data);
    }

    public function buildWithoutSaving(): Club
    {
        return factory(Club::class)->make($this->data);
    }
}
