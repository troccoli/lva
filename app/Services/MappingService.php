<?php

namespace LVA\Services;

use LVA\Models\Team;
use LVA\Models\Venue;


/**
 * Class MappingService
 *
 * @package LVA\Services
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
        foreach (Team::all() as $team) {
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
        foreach (Venue::all() as $venue) {
            $mappings[] = [
                'value' => $venue->id,
                'text'  => $venue->venue,
            ];
        }

        return $mappings;
    }
}