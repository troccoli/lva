<?php

namespace LVA\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Division
 *
 * @package LVA\Models
 */
class Division extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'divisions';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['season_id', 'division'];

    /**
     * @param string $division
     *
     * @return Division|null
     */
    public static function findByName($seasonId, $division)
    {
        return self::where('season_id', $seasonId)->where('division', $division)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fixtures()
    {
        return $this->hasMany(Fixture::class);
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
    public function getName()
    {
        return $this->division;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->season . ' ' . $this->division;
    }
}
