<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $fillable = ['club_id', 'name', 'venue_id'];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function getVenue():? Venue
    {
        if (is_null($this->venue)) {
            return $this->getClub()->getVenue();
        }

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

    public function getDivisions(): Collection
    {
        return $this->divisions;
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
