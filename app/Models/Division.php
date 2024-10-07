<?php

namespace App\Models;

use App\Events\DivisionCreated;
use App\Models\Builders\DivisionBuilder;
use App\Models\Contracts\Selectable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $competition_id
 * @property string $name
 * @property int $display_order
 * @property-read Competition $competition
 * @property-read Collection $teams
 * @property-read Collection $fixtures
 *
 * @method DivisionBuilder query()
 */
class Division extends Model implements Selectable
{
    use HasFactory,
        HasUuids,
        SoftDeletes;

    /** @var array<int, string> */
    protected $fillable = [
        'competition_id',
        'name',
        'display_order',
    ];

    protected $dispatchesEvents = [
        'created' => DivisionCreated::class,
    ];

    public function newEloquentBuilder($query): DivisionBuilder
    {
        return new DivisionBuilder($query);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
