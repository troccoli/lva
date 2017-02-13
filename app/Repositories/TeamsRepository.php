<?php


namespace LVA\Repositories;


use LVA\Models\MappedTeam;
use LVA\Models\Team;
use LVA\Models\TeamSynonym;
use LVA\Models\UploadJob;

/**
 * Class TeamRepository
 *
 * @package LVA\Repositories
 */
class TeamsRepository
{
    /** @var Team[] */
    private $modelsById;
    /** @var Team[] */
    private $modelsByName;
    private $mappedModelsByName;

    /**
     * @param int $id
     *
     * @return Team|null
     */
    public function findById($id)
    {
        if (isset($this->modelsById[$id])) {
            return $this->modelsById[$id];
        }

        /** @var Team $model */
        $model = Team::find($id);
        if ($model) {
            $this->modelsById[$id] = $model;
            return $model;
        }
        return null;
    }

    /**
     * @param string $name
     *
     * @return Team|null
     */
    public function findByName($name)
    {
        if (isset($this->modelsByName[$name])) {
            return $this->modelsByName[$name];
        }

        /** @var Team $model */
        $model = Team::findByName($name);
        if ($model) {
            $this->modelsByName[$name] = $model;
            return $model;
        } else {
            /** @var TeamSynonym $modelSynonym */
            $modelSynonym = TeamSynonym::findBySynonym($name);
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
            /** @var MappedTeam $mappedTeam */
            foreach (MappedTeam::findByJob($jobId) as $mappedTeam) {
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