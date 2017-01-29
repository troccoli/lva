<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UploadJob
 *
 * @package App\Models
 */
class UploadJob extends Model
{
    const TYPE_FIXTURES = 'fixtures';

    const STATUS_NOT_STARTED = 0;
    const STATUS_VALIDATING_RECORDS = 1;
    const STATUS_INSERTING_RECORDS = 2;
    const STATUS_UNKNOWN_DATA = 11;
    const STATUS_UNKNOWN_VENUE = 12;
    const STATUS_DONE = 99;

    protected $table = 'upload_jobs';
    protected $fillable = ['file', 'row_count', 'type', 'status'];

    public static function getStatusMessage($statusCode)
    {
        switch ($statusCode) {
            case self::STATUS_NOT_STARTED:
                return 'Not started';
            case self::STATUS_VALIDATING_RECORDS:
                return 'Validating records';
            case self::STATUS_INSERTING_RECORDS:
                return 'Inserting records';
            case self::STATUS_UNKNOWN_DATA:
                return 'Unknown data';
            case self::STATUS_DONE:
                return 'Done';
            default:
                return "Status code $statusCode not recognised";
        }
    }

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
    public function newTeams()
    {
        return $this->hasMany(NewTeams::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function newVenues()
    {
        return $this->hasMany(NewVenues::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploadData()
    {
        return $this->hasMany(UploadJobData::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
