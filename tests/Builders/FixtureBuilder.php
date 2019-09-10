<?php

namespace Tests\Builders;

use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Carbon\Carbon;

class FixtureBuilder
{
    private $data = [];

    public function number(int $matchNumber): self
    {
        $this->data['match_number'] = $matchNumber;

        return $this;
    }

    public function inDivision(Division $division): self
    {
        $this->data['division_id'] = $division->getId();

        return $this;
    }

    public function between(Team $homeTeam, Team $awayTeam): self
    {
        $this->data['home_team_id'] = $homeTeam->getId();
        $this->data['away_team_id'] = $awayTeam->getId();

        return $this;
    }

    public function on(Carbon $date, ?Carbon $time = null): self
    {
        $this->data['match_date'] = $date;
        if ($time) {
            $this->data['match_time'] = $time;
        }

        return $this;
    }

    public function at(Venue $venue): self
    {
        $this->data['venue_id'] = $venue->getId();

        return $this;
    }

    public function build(): Fixture
    {
        return factory(Fixture::class)->create($this->data);
    }
}
