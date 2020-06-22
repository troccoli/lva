<?php

namespace App\Repositories;

use App\Helpers\PermissionsHelper;
use App\Models\Competition;
use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AccessibleDivisions
{
    private Builder $query;

    public function __construct()
    {
        $this->reset();
    }

    public function get(User $user): Collection
    {
        $divisions = $this->query->get()
            ->filter(function (Division $division) use ($user): bool {
                return $user->can(PermissionsHelper::viewDivision($division));
            });

        $this->reset();

        return $divisions;
    }

    public function inCompetition(Competition $competition): self
    {
        $this->query->InCompetition($competition);

        return $this;
    }

    private function reset(): void
    {
        $this->query = Division::query();
    }
}
