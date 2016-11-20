<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Venue
 * @package App\Models
 */
class Venue extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'venues';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['venue'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fixtures()
    {
        return $this->hasMany(Fixture::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function synonyms()
    {
        return $this->hasMany(VenueSynonym::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mapped()
    {
        return $this->hasMany(MappedVenue::class);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->venue;
    }
}
