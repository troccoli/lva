<?php

namespace App\Models;

use App\Events\SeasonCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Season extends Model
{
    protected $fillable = ['year'];

    protected $dispatchesEvents = [
        'created' => SeasonCreated::class,
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getName(): string
    {
        return sprintf('%4u/%02u', $this->year, ($this->year + 1) % 100);
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    public function getCompetitions(): EloquentCollection
    {
        return $this->competitions;
    }

    public function getFixtures(): Collection
    {
        return $this->getCompetitions()->map(function (Competition $competition): Collection {
            return $competition->getFixtures();
        })->flatten();
    }

    public function scopeOrderedByYear(Builder $query): Builder
    {
        return $query->orderBy('year');
    }
}
