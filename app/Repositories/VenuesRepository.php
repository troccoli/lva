<?php

namespace LVA\Repositories;

use LVA\Models\MappedVenue;
use LVA\Models\UploadJob;
use LVA\Models\Venue;
use LVA\Models\VenueSynonym;

/**
 * Class VenueRepository.
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
            $model = VenueSynonym::findBySynonym($name);
            if ($model) {
                $this->modelsByName[$name] = $model;

                return $model;
            }
        }
    }

    public function findByNameWithinMapped(UploadJob $job, $venue)
    {
        $jobId = $job->getId();
        if (is_null($this->mappedModelsByName[$jobId])) {
            /** @var MappedVenue $mappedVenue */
            foreach (MappedVenue::findByJob($jobId) as $mappedVenue) {
                $this->mappedModelsByName[$jobId][$mappedVenue->getName()] = $mappedVenue->venue;
            }
        }

        if (isset($this->mappedModelsByName[$jobId][$venue])) {
            return $this->mappedModelsByName[$jobId][$venue];
        } else {
            return;
        }
    }
}
