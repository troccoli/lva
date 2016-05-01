<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Team
 *
 * @property-read \App\Models\Club $club
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Fixture[] $awayFixtures
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Fixture[] $homeFixtures
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $club_id
 * @property string $team
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Team whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Team whereClubId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Team whereTeam($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Team whereUpdatedAt($value)
 */
class Team extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'teams';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['club_id', 'team'];

    public function club()
    {
        return $this->belongsTo('App\Models\Club');
    }

    public function awayFixtures()
    {
        return $this->hasMany('App\Models\Fixture', 'away_team_id');
    }

    public function homeFixtures()
    {
        return $this->hasMany('App\Models\Fixture', 'home_team_id');
    }

    public function __toString()
    {
        return $this->team;
    }
}
