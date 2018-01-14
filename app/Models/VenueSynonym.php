<?php

namespace LVA\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VenueSynonym.
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
     * @param string $synonym
     *
     * @return Venue|null
     */
    public static function findBySynonym($synonym)
    {
        /** @var VenueSynonym $synonym */
        $synonym = self::where('synonym', $synonym)->first();
        if ($synonym) {
            return $synonym->venue;
        }
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
     * @return string
     */
    public function getSynonym()
    {
        return $this->synonym;
    }

    /**
     * @param string $synonym
     *
     * @return VenueSynonym
     */
    public function setSynonym($synonym)
    {
        $this->synonym = $synonym;

        return $this;
    }

    /**
     * @param string $venue
     *
     * @return VenueSynonym
     */
    public function setVenue($venue)
    {
        $this->venue()->associate(Venue::findByName($venue));

        return $this;
    }
}
