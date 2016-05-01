<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Division
 *
 * @property-read \App\Models\Season $season
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Fixture[] $fixtures
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $season_id
 * @property string $division
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Division whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Division whereSeasonId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Division whereDivision($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Division whereUpdatedAt($value)
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

    public function season()
    {
        return $this->belongsTo('App\Models\Season');
    }
    
    public function fixtures()
    {
        return $this->hasMany('App\Models\Fixture');
    }

    public function __toString()
    {
        return $this->season . ' ' . $this->division;
    }
}
