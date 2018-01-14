<?php

namespace LVA\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Club.
 */
class Club extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'clubs';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['club'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams()
    {
        return $this->hasMany(Team::class);
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
    public function getName()
    {
        return $this->club;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->club;
    }
}
