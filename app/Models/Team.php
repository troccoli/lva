<?php

namespace App\Models;

use App\Events\TeamCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Team extends Model
{
    protected $fillable = ['club_id', 'name', 'venue_id'];

    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSecretaryRole(): string
    {
        return "Team $this->id Secretary";
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function getClub(): Club
    {
        return $this->club;
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /*
     * It is necessary to get the venue using an accessor method
     * as the venue relationship is retrieved directly using
     * $this->venue in WhenLoaded() method
     */
    public function getVenueAttribute():? Venue
    {
        if ($this->venue_id === null) {
            return $this->getClub()->getVenue();
        }

        return Venue::find($this->venue_id);
    }
    public function getVenue():? Venue
    {
        return $this->venue;
    }

    public function getVenueId():? string
    {
        return $this->venue_id;
    }

    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(Division::class);
    }

    public function getDivisions(): EloquentCollection
    {
        return $this->divisions;
    }

    public function homeFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'home_team_id', 'id');
    }

    public function awayFixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'away_team_id', 'id');
    }

    public function getFixtures(): Collection
    {
        return collect([
            $this->homeFixtures,
            $this->awayFixtures,
        ])->flatten();
    }

    public function scopeInClub(Builder $query, Club $club): Builder
    {
        return $query->where('club_id', $club->getId());
    }

    public function scopeOrderByName(Builder $query): Builder
    {
        return $query->orderBy('name');
    }
}
