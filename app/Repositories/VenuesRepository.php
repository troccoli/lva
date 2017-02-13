<?php


namespace Repositories;

use LVA\Models\MappedVenue;
use LVA\Models\Venue;
use LVA\Models\VenueSynonym;
use LVA\Models\UploadJob;

/**
 * Class VenueRepository
 *
 * @package Repositories
 */
class VenuesRepository
{
    /** @var Venue[] */
    private $modelsById;
    /** @var Venue[] */
    private $modelsByName;
    private $mappedModelsByName;

    /**
     * @param int $id
     *
     * @return Venue|null
     */
    public function findById($id)
    {
        if (isset($this->modelsById[$id])) {
            return $this->modelsById[$id];
        }

        /** @var Venue $model */
        $model = Venue::find($id);
        if ($model) {
            $this->modelsById[$id] = $model;
            return $model;
        }
        return null;
    }

    /**
     * @param string $name
     *
     * @return Venue|null
     */
    public function findByName($name)
    {
        if (isset($this->modelsByName[$name])) {
            return $this->modelsByName[$name];
        }

        /** @var Venue $model */
        $model = Venue::findByName($name);
        if ($model) {
            $this->modelsByName[$name] = $model;
            return $model;
        } else {
            /** @var VenueSynonym $modelSynonym */
            $modelSynonym = VenueSynonym::findBySynonym($name);
            if ($modelSynonym) {
                $$model = $modelSynonym->team;
                $this->modelsByName[$name] = $model;
                return $model;
            }
        }
        return null;
    }

    public function findByNameWithinMapped(UploadJob $job, $team)
    {
        $jobId = $job->getId();
        if (is_null($this->mappedModelsByName[$jobId])) {
            /** @var MappedVenue $mappedTeam */
            foreach (MappedVenue::findByJob($jobId) as $mappedTeam) {
                $this->mappedModelsByName[$jobId][$mappedTeam->getName()] = $mappedTeam->team;
            }
        }

        if (isset($this->mappedModelsByName[$jobId][$team])) {
            return $this->mappedModelsByName[$jobId][$team];
        } else {
            return null;
        }
    }
}