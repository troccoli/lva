<?php

namespace App\Models;

use App\Events\TeamCreated;
use App\Models\Contracts\Selectable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $club_id
 * @property string $name
 * @property ?string $venue_id
 * @property-read Club $club
 * @property-read ?Venue $venue
 * @property-read Collection $homeFixtures
 * @property-read Collection $awayFixtures
 */
class Team extends Model implements Selectable
{
    use HasFactory,
        HasUuids;

    /** @var array<int, string> */
    protected $fillable = [
        'club_id',
        'name',
        'venue_id',
    ];

    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(Division::class);
    }

    public function homeFixtures(): HasMany
    {
        return $this->hasMany(related: Fixture::class, foreignKey: 'home_team_id', localKey: 'id');
    }

    public function awayFixtures(): HasMany
    {
        return $this->hasMany(related: Fixture::class, foreignKey: 'away_team_id', localKey: 'id');
    }

    public function getName(): string
    {
        return $this->name;
    }
}
