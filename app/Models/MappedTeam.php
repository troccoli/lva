<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 15:28
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MappedTeam
 *
 * @package App\Models
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
    protected $fillable = ['upload_job_id', 'team', 'team_id'];

    /**
     * @param $jobId
     *
     * @return Collection
     */
    public static function findByJob($jobId)
    {
        return MappedTeam::where('upload_job_id', $jobId)->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function uploadJob()
    {
        return $this->hasOne(UploadJob::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function team()
    {
        return $this->hasOne(Team::class);
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
        return $this->team;
    }

    /**
     * @param string $team
     *
     * @return MappedTeam
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @param string $team
     *
     * @return MappedTeam
     */
    public function setMappedTeam($team)
    {
        $this->team_id = Team::findByName($team);

        return $this;
    }
}