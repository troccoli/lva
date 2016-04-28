<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
