<?php

namespace App\Models;

use App\Models\Builders\FixtureBuilder;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $match_number
 * @property string $division_id
 * @property string home_team_id
 * @property string away_team_id
 * @property CarbonImmutable $match_date
 * @property-read CarbonImmutable $match_datetime
 * @property string venue_id
 * @property-read Division $division
 * @property-read Team homeTeam
 * @property-read Team awayTeam
 * @property-read Venue $venue
 */
class Fixture extends Model
{
    use HasFactory,
        HasUuids,
        SoftDeletes;

    protected $guarded = [
        'id',
        self::CREATED_AT,
        self::UPDATED_AT,
        'deleted_at',
    ];

    protected $casts = [
        'match_date' => 'immutable_date',
    ];

    public function newEloquentBuilder($query): Builder
    {
        return new FixtureBuilder($query);
    }

    protected function matchDatetime(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): CarbonImmutable {
                /** @var Carbon $matchDatetime */
                $matchDatetime = Carbon::create($attributes['match_date']);
                $matchDatetime->setTimeFrom($attributes['start_time']);

                return $matchDatetime->toImmutable();
            },
        );
    }

    protected function startTime(): Attribute
    {
        return Attribute::make(
            set: function (string|Carbon $value): string {
                if ($value instanceof Carbon) {
                    return $value->format('H:i');
                }

                return $value;
            },
        );
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function homeTeam(): HasOne
    {
        return $this->hasOne(related: Team::class, foreignKey: 'id', localKey: 'home_team_id');
    }

    public function awayTeam(): HasOne
    {
        return $this->hasOne(related: Team::class, foreignKey: 'id', localKey: 'away_team_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
