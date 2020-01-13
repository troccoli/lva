<?php

namespace App\Http\Resources;

use App\Models\Fixture;
use Illuminate\Http\Resources\Json\JsonResource;

class FixtureResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Fixture $fixture */
        $fixture = $this->resource;

        return [
            'id'             => $fixture->getId(),
            'number'         => $fixture->getMatchNumber(),
            'division'       => $fixture->getDivision()->getName(),
            'division_id'    => $fixture->getDivision()->getId(),
            'home_team'      => $fixture->getHomeTeam()->getName(),
            'home_team_id'   => $fixture->getHomeTeam()->getId(),
            'away_team'      => $fixture->getAwayTeam()->getName(),
            'away_team_id'   => $fixture->getAwayTeam()->getId(),
            'date'           => $fixture->getMatchDate()->toDateString(),
            'time'           => $fixture->getMatchTime()->format('H:i'),
            'venue'          => $fixture->getVenue()->getName(),
            'venue_id'       => $fixture->getVenue()->getId(),
        ];
    }
}
