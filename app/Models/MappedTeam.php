<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 15:28
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MappedTeam
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
}