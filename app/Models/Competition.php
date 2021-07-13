<?php

namespace App\Models;

use App\Events\CompetitionCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = ['season_id', 'name'];

    protected $dispatchesEvents = [
        'created' => CompetitionCreated::class,
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function getDivisions(): EloquentCollection
    {
        return $this->divisions;
    }

    public function fixtures(): HasManyThrough
    {
        return $this->hasManyThrough(Fixture::class, Division::class);
    }

    public function getFixtures(): EloquentCollection
    {
        return $this->fixtures;
    }

    public function scopeInSeason(Builder $query, Season $season): Builder
    {
        return $query->where('season_id', $season->getId());
    }

    public function scopeOrderByName(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    public function scopeOrderBySeasonDesc(Builder $query): Builder
    {
        return $query->orderByDesc('season_id');
    }
}
