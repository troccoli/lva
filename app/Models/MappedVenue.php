<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli-Allard <giulio@troccoli.it>
 * Date: 20/11/2016
 * Time: 16:05
 */

namespace LVA\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class MappedVenue
 *
 * @package LVA\Models
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
    protected $fillable = ['upload_job_id', 'mapped_venue', 'venue_id'];

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
    public function venue()
    {
        return $this->belongsTo(Venue::class);
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
        return $this->mapped_venue;
    }

    /**
     * @param string $venue
     *
     * @return MappedVenue
     */
    public function setName($venue)
    {
        $this->mapped_venue = $venue;

        return $this;
    }

    /**
     * @param string $venue
     *
     * @return MappedVenue
     */
    public function setMappedVenue($venue)
    {
        $this->venue_id = Venue::findByName($venue)->getId();

        return $this;
    }
}