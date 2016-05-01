<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Club
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @mixin \Eloquent
 * @property integer $id
 * @property string $club
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Club whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Club whereClub($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Club whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Club whereUpdatedAt($value)
 */
class Club extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'clubs';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['club'];

    public function teams()
    {
        return $this->hasMany('App\Models\Team');
    }

    public function __toString()
    {
        return $this->club;
    }
}
