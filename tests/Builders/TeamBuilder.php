<?php

namespace Tests\Builders;

use App\Models\Club;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamBuilder
{
    private $data = [];
    private $sortBy = '';

    public function withName(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function inClub(Club $club): self
    {
        $this->data['club_id'] = $club->getId();

        return $this;
    }

    public function orderedByName(): self
    {
        $this->sortBy = 'name';

        return $this;
    }
    /**
     * @return Team|Collection
     */
    public function build(int $number = 1)
    {
        if ($number === 1) {
            return factory(Team::class)->create($this->data);
        }

        $teams = factory(Team::class, $number)->create($this->data);

        if (!empty($this->sortBy)) {
            $teams = $teams->sortBy($this->sortBy);
        }

        return $teams;
    }

    /**
     * @return Team|Collection
     */
    public function buildWithoutSaving(int $number = 1)
    {
        if ($number === 1) {
            return factory(Team::class)->make($this->data);
        }

        return factory(Team::class)->times($number)->make($this->data);
    }
}
