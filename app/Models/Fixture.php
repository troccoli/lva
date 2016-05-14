<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Fixture
 *
 * @property-read \App\Models\Division $division
 * @property-read \App\Models\Team $home_team
 * @property-read \App\Models\Team $away_team
 * @property-read \App\Models\Venue $venue
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AvailableAppointment[] $available_appointments
 * @property-read mixed $warm_up_time
 * @property-read mixed $start_time
 * @property-read mixed $match_date
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $division_id
 * @property integer $match_number
 * @property integer $home_team_id
 * @property integer $away_team_id
 * @property integer $venue_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereDivisionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereMatchNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereMatchDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereWarmUpTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereHomeTeamId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereAwayTeamId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereVenueId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Fixture whereUpdatedAt($value)
 */
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

    public function available_appointments()
    {
        return $this->hasMany('App\Models\AvailableAppointment');
    }
    
    public function getWarmUpTimeAttribute($time)
    {
        return Carbon::createFromFormat('H:i:s', $time);
    }

    public function getStartTimeAttribute($time)
    {
        return Carbon::createFromFormat('H:i:s', $time);
    }

    public function getMatchDateAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date);
    }

    public function __toString()
    {
        return
            $this->division . ':' . $this->match_number . ' ' .
            $this->match_date->format('d/m/y') . ' ' .
            $this->start_time->format('H:i') . '(' . $this->warm_up_time->format('H:i') . ') ' .
            $this->home_team . ' v ' . $this->away_team;
    }
}
