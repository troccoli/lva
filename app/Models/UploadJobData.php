<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 15:56
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UploadJobData
 * @package App\Models
 */
class UploadJobData extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'upload_jobs_data';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['upload_job_id', 'model_data'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function uploadJob()
    {
        return $this->hasOne(UploadJob::class);
    }
}