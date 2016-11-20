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
 * @package App\Models
 */
class NewVenues extends Model
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
    protected $fillable = ['upload_job_id', 'venue', 'venue_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function uploadJob()
    {
        return $this->hasOne(UploadJob::class);
    }
}