<?php

namespace Tests\Builders;

use App\Models\Club;

class ClubBuilder
{
    private $data = [];

    public function withName(string $name): self
    {
        $this->data['name'] = $name;

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
