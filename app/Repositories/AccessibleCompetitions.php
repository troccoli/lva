<?php

namespace App\Repositories;

use App\Helpers\PermissionsHelper;
use App\Models\Competition;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AccessibleCompetitions
{
    private Builder $query;

    public function __construct()
    {
        $this->reset();
    }

    public function get(User $user): Collection
    {
        $competitions = $this->query->get()
            ->filter(function (Competition $competition) use ($user): bool {
                return $user->can(PermissionsHelper::viewCompetition($competition)) ||
                    $user->can(PermissionsHelper::viewCompetitions($competition->getSeason()));
            });

        $this->reset();

        return $competitions;
    }

    public function inSeason(Season $season): self
    {
        $this->query->InSeason($season);

        return $this;
    }

    private function reset(): void
    {
        $this->query = Competition::query();
    }
}
