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

    public function getStatus($value)
    {
        return json_decode($value);
    }

    public function setStatus($value)
    {
        return json_encode($value);
    }

    /**
     * @inheritDoc
     */
    public function save(array $options = [])
    {
        // Fire the "status updated" event
        return parent::save($options);
    }


}
