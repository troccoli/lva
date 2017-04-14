<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 15:28
 */

namespace LVA\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MappedTeam
 *
 * @package LVA\Models
 */
class MappedTeam extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mapped_teams';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['upload_job_id', 'mapped_team', 'team_id'];

    /**
     * @param $jobId
     *
     * @return Collection
     */
    public static function findByJob($jobId)
    {
        return self::where('upload_job_id', $jobId)->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uploadJob()
    {
        return $this->belongsTo(UploadJob::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $jobId
     *
     * @return MappedTeam
     */
    public function setUploadJob($jobId)
    {
        $this->upload_job_id = $jobId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->mapped_team;
    }

    /**
     * @param string $team
     *
     * @return MappedTeam
     */
    public function setName($team)
    {
        $this->mapped_team = $team;

        return $this;
    }

    /**
     * @param string $team
     *
     * @return MappedTeam
     */
    public function setMappedTeam($team)
    {
        $this->team()->associate(Team::findByName($team));

        return $this;
    }
}