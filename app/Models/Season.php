<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    protected $fillable = ['year'];

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

    public function getCompetitions(): Collection
    {
        return $this->competitions;
    }

    public function scopeOrderedByYear(Builder $query): Builder
    {
        return $query->orderBy('year');
    }
}
