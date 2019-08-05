<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use SoftDeletes;

    protected $fillable = ['competition_id', 'name', 'display_order'];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function scopeInOrder(Builder $builder): Builder
    {
        return $builder->orderBy('display_order');
    }

    public function scopeInCompetition(Builder $builder, Competition $competition): Builder
    {
        return $builder->where('competition_id', $competition->getId());
    }
}
