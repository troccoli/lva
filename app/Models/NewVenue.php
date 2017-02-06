<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 16:09
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class NewVenues
 *
 * @package App\Models
 */
class NewVenue extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'new_venues';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['upload_job_id', 'venue'];

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
     * @return NewVenue
     */
    public function setUploadJob($jobId)
    {
        $this->upload_job_id = $jobId;

        return $this;
    }

    /**
     * @param string $venue
     *
     * @return NewVenue
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;

        return $this;
    }
}