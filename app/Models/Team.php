<?php

namespace LVA\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Team
 *
 * @package LVA\Models
 */
class Team extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'teams';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['club_id', 'team'];

    /**
     * @param string $team
     *
     * @return Team|null
     */
    public static function findByName($team)
    {
        return self::where('team', $team)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function awayFixtures()
    {
        return $this->hasMany(Fixture::class, 'away_team_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function homeFixtures()
    {
        return $this->hasMany(Fixture::class, 'home_team_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function synonyms()
    {
        return $this->hasMany(TeamSynonym::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mapped()
    {
        return $this->hasMany(MappedTeam::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->team;
    }
}
