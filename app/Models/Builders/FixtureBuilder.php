<?php

namespace App\Models\Builders;

use App\Models\Division;
use Illuminate\Database\Eloquent\Builder;

class FixtureBuilder extends Builder
{
    public function inDivision(Division|string $division): self
    {
        if ($division instanceof Division) {
            $division = $division->getKey();
        }

        $this->query->where('division_id', $division);

        return $this;
    }
}
