<?php

namespace Tests\Builders;

use App\Models\Club;
use App\Models\Division;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Collection;

class TeamBuilder
{
    private $data = [];
    private $sortBy = '';
    /** @var Division */
    private $division = null;

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

    public function withVenue(Venue $venue): self
    {
        $this->data['venue_id'] = $venue->getId();

        return $this;
    }

    public function inDivision(Division $division): self
    {
        $this->division = $division;

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
            /** @var Team $team */
            $team =  factory(Team::class)->create($this->data);

            if ($this->division) {
                $team->divisions()->attach($this->division);
            }

            return $team;
        }

        $teams = factory(Team::class, $number)->create($this->data);

        if ($this->division) {
            $teams->each(function (Team $team): void {
                $team->divisions()->attach($this->division);
            });
        }

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
