<?php

namespace App\Models;

use App\Models\Contracts\Selectable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property-read Collection $teams
 * @property-read ?Venue $venue
 */
class Club extends Model implements Selectable
{
    use HasFactory,
        HasUuids;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'venue_id',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
