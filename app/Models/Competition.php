<?php

namespace App\Models;

use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Competition extends Model
{
    protected $fillable = ['season_id', 'name'];

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
