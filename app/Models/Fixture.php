<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Fixture
 * @package App\Models
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
        'venue_id',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function home_team()
    {
        return $this->belongsTo(Team::class);
    }

    public function away_team()
    {
        return $this->belongsTo(Team::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function available_appointments()
    {
        return $this->hasMany(AvailableAppointment::class);
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
