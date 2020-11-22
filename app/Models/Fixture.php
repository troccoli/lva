<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
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

    public function homeTeam(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'home_team_id');
    }

    public function getHomeTeam(): Team
    {
        return $this->homeTeam;
    }

    public function awayTeam(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'away_team_id');
    }

    public function getAwayTeam(): Team
    {
        return $this->awayTeam;
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function getVenue(): Venue
    {
        return $this->venue;
    }

    public function scopeInSeason(Builder $builder, Season $season): Builder
    {
        return $builder->whereHas('division.competition.season', function (Builder $builder) use ($season): Builder {
            return $builder->where('id', $season->getId());
        });
    }

    public function scopeInCompetition(Builder $builder, Competition $competition): Builder
    {
        return $builder->whereHas('division.competition', function (Builder $builder) use ($competition): Builder {
            return $builder->where('id', $competition->getId());
        });
    }

    public function scopeInDivision(Builder $builder, Division $division): Builder
    {
        return $builder->where('division_id', $division->getId());
    }

    public function scopeForTeam(Builder $builder, Team $team): Builder
    {
        return $builder->where(function (Builder $builder) use ($team): Builder {
            return $builder->where('home_team_id', $team->getId())
                ->orWhere('away_team_id', $team->getId());
        });
    }

    public function scopeForHomeTeam(Builder $builder, Team $team): Builder
    {
        return $builder->where('home_team_id', $team->getId());
    }

    public function scopeForAwayTeam(Builder $builder, Team $team): Builder
    {
        return $builder->where('away_team_id', $team->getId());
    }

    public function scopeOn(Builder $builder, Carbon $date): Builder
    {
        return $builder->whereBetween('match_date', [$date->startOfDay(), $date->copy()->endOfDay()]);
    }

    public function scopeAt(Builder $builder, Venue $venue): Builder
    {
        return $builder->where('venue_id', $venue->getId());
    }
}
