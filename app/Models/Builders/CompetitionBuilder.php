<?php

namespace App\Models\Builders;

use App\Models\Season;
use Illuminate\Database\Eloquent\Builder;

class CompetitionBuilder extends Builder
{
    public function inSeason(Season|string $season): self
    {
        if ($season instanceof Season) {
            $season = $season->getKey();
        }

        $this->query->where('season_id', $season);

        return $this;
    }
}
