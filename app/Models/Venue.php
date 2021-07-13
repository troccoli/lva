<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory, UuidAsKey;

    public $incrementing = false;
    protected $fillable = ['name'];
    protected $keyType = 'string';

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function getClubs(): Collection
    {
        return $this->clubs;
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }

    public function getFixtures(): Collection
    {
        return $this->fixtures;
    }
}
