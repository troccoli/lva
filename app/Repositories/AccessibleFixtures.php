<?php

namespace App\Repositories;

use App\Helpers\PermissionsHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AccessibleFixtures
{
    private Builder $query;

    public function __construct()
    {
        $this->reset();
    }

    public function get(User $user): Collection
    {
        $fixtures = $this->query
            ->with(['division', 'venue', 'homeTeam', 'awayTeam'])
            ->orderBy('match_number')
            ->get()
            ->filter(function (Fixture $fixture) use ($user): bool {
                return $user->can(PermissionsHelper::viewDivision($fixture->getDivision()));
            });

        $this->reset();

        return $fixtures;
    }

    public function inSeason(Season $season): self
    {
        $this->query->inSeason($season);

        return $this;
    }

    public function inCompetition(Competition $competition): self
    {
        $this->query->inCompetition($competition);

        return $this;
    }

    public function inDivision(Division $division): self
    {
        $this->query->inDivision($division);

        return $this;
    }

    public function on(Carbon $date): self
    {
        $this->query->on($date);

        return $this;
    }

    public function forTeam(Team $team): self
    {
        $this->query->forTeam($team);

        return $this;
    }

    public function forHomeTeam(Team $team): self
    {
        $this->query->forHomeTeam($team);

        return $this;
    }

    public function forAwayTeam(Team $team): self
    {
        $this->query->forAwayTeam($team);

        return $this;
    }

    public function at(Venue $venue): self
    {
        $this->query->at($venue);

        return $this;
    }

    private function reset(): void
    {
        $this->query = Fixture::query();
    }
}
