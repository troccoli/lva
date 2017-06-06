<?php

namespace LVA\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Fixture
 *
 * @package LVA\Models
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
        'notes',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function home_team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function away_team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function available_appointments()
    {
        return $this->hasMany(AvailableAppointment::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $divisionId
     *
     * @return Fixture
     */
    public function setDivision($divisionId)
    {
        $this->division_id = $divisionId;

        return $this;
    }

    /**
     * @param int $macthNumber
     *
     * @return Fixture
     */
    public function setMatchNumber($macthNumber)
    {
        $this->match_number = $macthNumber;

        return $this;
    }

    /**
     * @param Carbon $date
     *
     * @return Fixture
     */
    public function setMatchDate(Carbon $date)
    {
        $this->match_date = $date->format('Y-m-d');

        return $this;
    }

    /**
     * @param Carbon $time
     *
     * @return Fixture
     */
    public function setWarmUpTime(Carbon $time)
    {
        $this->warm_up_time = $time->format('H:i:s');

        return $this;
    }

    /**
     * @param Carbon $time
     *
     * @return Fixture
     */
    public function setStartTime(Carbon $time)
    {
        $this->start_time = $time->format('H:i:s');

        return $this;
    }

    /**
     * @param int $teamId
     *
     * @return Fixture
     */
    public function setHomeTeam($teamId)
    {
        $this->home_team_id = $teamId;

        return $this;
    }

    /**
     * @param int $teamId
     *
     * @return Fixture
     */
    public function setAwayTeam($teamId)
    {
        $this->away_team_id = $teamId;

        return $this;
    }

    /**
     * @param int $venueId
     *
     * @return Fixture
     */
    public function setVenue($venueId)
    {
        $this->venue_id = $venueId;

        return $this;
    }

    /**
     * @param string $time
     *
     * @return Carbon
     */
    public function getWarmUpTimeAttribute($time)
    {
        return Carbon::createFromFormat('H:i:s', $time);
    }

    /**
     * @param string $time
     *
     * @return Carbon
     */
    public function getStartTimeAttribute($time)
    {
        return Carbon::createFromFormat('H:i:s', $time);
    }

    /**
     * @param string $date
     *
     * @return Carbon
     */
    public function getMatchDateAttribute($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date);
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return
            $this->division . ':' . $this->match_number . ' ' .
            $this->match_date->format('d/m/y') . ' ' .
            $this->start_time->format('H:i') . '(' . $this->warm_up_time->format('H:i') . ') ' .
            $this->home_team . ' v ' . $this->away_team;
    }
}
