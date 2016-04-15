<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fixtures';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'division_id',
        'match_number',
        'match_date',
        'warm_up_time',
        'start_time',
        'home_team_id',
        'away_team_id',
        'venue_id'
    ];

    protected $dates = ['match_date'];

    public function division()
    {
        return $this->belongsTo('App\Models\Division');
    }

    public function home_team()
    {
        return $this->belongsTo('App\Models\Team');
    }

    public function away_team()
    {
        return $this->belongsTo('App\Models\Team');
    }

    public function venue()
    {
        return $this->belongsTo('App\Models\Venue');
    }
}
