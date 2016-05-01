<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Season
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Division[] $divisions
 * @mixin \Eloquent
 * @property integer $id
 * @property string $season
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Season whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Season whereSeason($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Season whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Season whereUpdatedAt($value)
 */
class Season extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'seasons';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['season'];

    public function divisions()
    {
        return $this->hasMany('App\Models\Division');
    }

    public function __toString()
    {
        return $this->season;
    }
}
