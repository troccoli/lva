<?php

namespace App\Models;

use App\Events\CompetitionCreated;
use App\Models\Builders\CompetitionBuilder;
use App\Models\Contracts\Selectable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $season_id
 * @property string $name
 * @property-read Season $season
 * @property-read Collection $divisions
 *
 * @method CompetitionBuilder query()
 */
class Competition extends Model implements Selectable
{
    use HasFactory,
        HasUuids;

    /** @var array<int, string> */
    protected $fillable = [
        'season_id',
        'name',
    ];

    protected $dispatchesEvents = [
        'created' => CompetitionCreated::class,
    ];

    public function newEloquentBuilder($query): CompetitionBuilder
    {
        return new CompetitionBuilder($query);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function divisions(): HasMany
    {
        return $this->hasMany(Division::class);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
