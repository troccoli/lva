<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VenueSynonym
 * @package App\Models
 */
class VenueSynonym extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'venues_synonyms';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['synonym', 'venue_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function venue()
    {
        return $this->hasOne(Venue::class);
    }
}
