<?php

namespace LVA\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VenueSynonym
 *
 * @package LVA\Models
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
        $synonym = self::where('synonym', $synonym)->first();
        if ($synonym) {
            return $synonym->venue;
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function venue()
    {
        return $this->hasOne(Venue::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
