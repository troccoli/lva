<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Club
 * @package App\Models
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

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function __toString()
    {
        return $this->club;
    }
}
