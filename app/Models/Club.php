<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = ['name', 'venue_id'];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function getVenue():? Venue
    {
        return $this->venue;
    }

    public function getVenueId():? string
    {
        return $this->venue_id;
    }
}
