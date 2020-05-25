<?php

namespace App\Models;

use App\Events\DivisionCreated;
use App\Events\SeasonCreated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use SoftDeletes;

    protected $fillable = ['competition_id', 'name', 'display_order'];

    protected $dispatchesEvents = [
        'created' => DivisionCreated::class,
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAdminRole(): string
    {
        return "Division $this->id Administrator";
    }

    public function getOrder(): int
    {
        return $this->display_order;
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function getCompetition(): Competition
    {
        return $this->competition;
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    public function getFixtures(): Collection
    {
        return $this->fixtures;
    }

    public function scopeInOrder(Builder $builder): Builder
    {
        return $builder->orderBy('display_order');
    }

    public function scopeInCompetition(Builder $builder, Competition $competition): Builder
    {
        return $builder->where('competition_id', $competition->getId());
    }
}
