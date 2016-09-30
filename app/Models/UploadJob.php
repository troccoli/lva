<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UploadJob
 *
 * @property integer $id
 * @property string $file
 * @property string $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UploadJob whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UploadJob whereFile($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UploadJob whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UploadJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UploadJob whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $status
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UploadJob whereStatus($value)
 */
class UploadJob extends Model
{
    const TYPE_FIXTURES = 'fixtures';

    const STATUS_NOT_STARTED = 0;
    const STATUS_VALIDATING_RECORDS = 1;
    const STATUS_INSERTING_RECORDS = 2;
    const STATUS_DONE = 99;

    protected $table = 'upload_jobs';
    protected $fillable = ['file', 'type', 'status'];

    /**
     * @param string $value
     * @return array
     */
    public function getStatusAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param array $value
     */
    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = json_encode($value);
    }
}
