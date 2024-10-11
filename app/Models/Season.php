<?php

namespace App\Models;

use App\Events\SeasonCreated;
use App\Models\Contracts\Selectable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $year
 * @property-read string $name
 * @property-read Collection $competitions
 */
class Season extends Model implements Selectable
{
    use HasFactory,
        HasUuids;

    /** @var array<int, string> */
    protected $fillable = [
        'year',
    ];

    protected $dispatchesEvents = [
        'created' => SeasonCreated::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (Season $season) {
            $season->name = sprintf('%4u/%02u', $season->year, ($season->year + 1) % 100);
        });
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
