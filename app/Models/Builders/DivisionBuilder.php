<?php

namespace App\Models\Builders;

use App\Models\Competition;
use Illuminate\Database\Eloquent\Builder;

class DivisionBuilder extends Builder
{
    public function inCompetition(Competition|string $competition): self
    {
        if ($competition instanceof Competition) {
            $competition = $competition->getKey();
        }

        $this->query->where('competition_id', $competition);

        return $this;
    }
}
