<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Season
 * @package App\Models
 */
class Season extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'seasons';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['season'];

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public function __toString()
    {
        return $this->season;
    }
}
