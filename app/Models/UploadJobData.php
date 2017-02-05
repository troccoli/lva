<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 15:56
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UploadJobData
 *
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
    protected $fillable = ['upload_job_id', 'model', 'model_data'];

    /**
     * @param $jobId
     *
     * @return Collection
     */
    public static function findByJobId($jobId)
    {
        return self::where('upload_job_id', $jobId)->get();
    }

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

    /**
     * @param int $jobId
     *
     * @return UploadJobData
     */
    public function setJobId($jobId)
    {
        $this->upload_job_id = $jobId;

        return $this;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     *
     * @return UploadJobData
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->model_data;
    }

    /**
     * @param string $data
     *
     * @return UploadJobData
     */
    public function setData($data)
    {
        $this->model_data = $data;

        return $this;
    }
}