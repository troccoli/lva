<?php

namespace LVA\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UploadJob.
 */
class UploadJob extends Model
{
    const TYPE_FIXTURES = 'fixtures';

    protected $table = 'upload_jobs';
    protected $fillable = ['season_id', 'file', 'row_count', 'type', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mappedTeams()
    {
        return $this->hasMany(MappedTeam::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mappedVenues()
    {
        return $this->hasMany(MappedVenue::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploadData()
    {
        return $this->hasMany(UploadJobData::class);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeStale(Builder $query)
    {
        return $query->where('updated_at', '<=', Carbon::now()->subWeek());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSeason()
    {
        return $this->season_id;
    }

    /**
     * @param int $seasonId
     *
     * @return UploadJob
     */
    public function setSeason($seasonId)
    {
        $this->season_id = $seasonId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $filename
     *
     * @return UploadJob
     */
    public function setFile($filename)
    {
        $this->file = $filename;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return UploadJob
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        return json_decode($this->status, true);
    }

    /**
     * @param array $status
     *
     * @return UploadJob
     */
    public function setStatus($status)
    {
        $this->status = json_encode($status);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRowCount()
    {
        return $this->row_count;
    }

    /**
     * @param int $count
     *
     * @return UploadJob
     */
    public function setRowCount($count)
    {
        $this->row_count = $count;

        return $this;
    }
}
