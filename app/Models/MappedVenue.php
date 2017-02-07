<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 16:05
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MappedVenue
 *
 * @package App\Models
 */
class MappedVenue extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mapped_venues';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['upload_job_id', 'venue', 'venue_id'];

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
    public function venue()
    {
        return $this->hasOne(Venue::class);
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
     * @return MappedVenue
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
        return $this->venue;
    }

    /**
     * @param string $venue
     *
     * @return MappedVenue
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;

        return $this;
    }

    /**
     * @param string $venue
     *
     * @return MappedVenue
     */
    public function setMappedVenue($venue)
    {
        $this->venue_id = Venue::findByName($venue);

        return $this;
    }
}