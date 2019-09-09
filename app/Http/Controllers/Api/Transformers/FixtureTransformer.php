<?php

namespace App\Http\Controllers\Api\Transformers;

use App\Models\Fixture;

class FixtureTransformer
{
    public function transform(Fixture $fixture): array
    {
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
