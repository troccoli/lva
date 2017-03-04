<?php

namespace LVA\Services;

use LVA\Models\Team;


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
        return [];
    }
}