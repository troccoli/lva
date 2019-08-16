<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fixture extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['match_date'];

    public function getId(): int
    {
        return $this->id;
    }

    public function getMatchNumber(): int
    {
        return $this->match_number;
    }

    public function getDivision(): Division
    {
        return $this->division;
    }

    public function getCompetition(): Competition
    {
        return $this->getDivision()->getCompetition();
    }

    public function getSeason(): Season
    {
        return $this->getCompetition()->getSeason();
    }

    public function getHomeTeam(): Team
    {
        return $this->homeTeam;
    }

    public function getAwayTeam(): Team
    {
        return $this->awayTeam;
    }

    public function getMatchDate(): Carbon
    {
        return $this->match_date;
    }

    public function getMatchTimeAttribute($time): Carbon
    {
        return Carbon::parse($time);
    }

    public function getMatchTime(): Carbon
    {
        return $this->match_time;
    }

    public function getVenue(): Venue
    {
        return $this->venue;
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function homeTeam(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'home_team_id');
    }

    public function awayTeam(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'away_team_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
