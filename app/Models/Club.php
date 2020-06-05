<?php

namespace App\Models;

use App\Events\ClubCreated;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Club extends Model
{
    protected $fillable = ['name', 'venue_id'];

    protected $dispatchesEvents = [
        'created' => ClubCreated::class,
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
        return "Club $this->id Secretary";
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function getTeams(): EloquentCollection
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

    public function getFixtures(): Collection
    {
        return $this->getTeams()->map(function (Team $team): Collection {
            return $team->getFixtures();
        })->flatten();
    }
}
