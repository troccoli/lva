<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 16:07
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NewTeams
 *
 * @package App\Models
 */
class NewTeams extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'new_teams';

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}