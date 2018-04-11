<?php

namespace LVA\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Venue.
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
    protected $fillable = ['venue', 'directions', 'postcode'];

    /**
     * @param string $venue
     *
     * @return Venue|null
     */
    public static function findByName($venue)
    {
        return self::where('venue', $venue)->first();
    }

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->venue;
    }

    /**
     * @return string
     */
    public function getDirections()
    {
        return $this->directions;
    }

    public function setPostcodeAttribute($postcode)
    {
        //remove non alphanumeric characters
        $cleanPostcode = preg_replace('/[^A-Za-z0-9]/', '', $postcode);

        //make uppercase
        $cleanPostcode = strtoupper($cleanPostcode);

        //insert space
        $postcode = substr($cleanPostcode, 0, -3) . ' ' . substr($cleanPostcode, -3);

        $this->attributes['postcode'] = $postcode;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->venue;
    }
}
