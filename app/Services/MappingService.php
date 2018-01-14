<?php

namespace LVA\Services;

use LVA\Models\Team;
use LVA\Models\Venue;

/**
 * Class MappingService.
 */
class MappingService
{
    /**
     * @param $divisionId
     * @param $team
     *
     * @return array
     */
    public function findTeamMappings($divisionId, $team)
    {
        $mappings = [];
        foreach (Team::orderby('team', 'asc')->get() as $team) {
            $mappings[] = [
                'value' => $team->id,
                'text'  => $team->team,
            ];
        }

        return $mappings;
    }

    /**
     * @param string $venue
     *
     * @return array
     */
    public function findVenueMappings($venue)
    {
        $mappings = [];
        /** @var Venue $venue */
        foreach (Venue::orderBy('venue', 'asc')->get() as $venue) {
            $mappings[] = [
                'value' => $venue->getId(),
                'text'  => $venue->getName(),
            ];
        }

        return $mappings;
    }
}
