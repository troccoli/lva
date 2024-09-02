<?php

namespace App\Models;

use App\Models\Contracts\Selectable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property-read Collection $clubs
 * @property-read Collection teams
 */
class Venue extends Model implements Selectable
{
    use HasFactory,
        HasUuids;

    protected $fillable = [
        'name',
    ];

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
