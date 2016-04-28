<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'venues';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['venue'];

    public function fixtures()
    {
        return $this->hasMany('App\Models\Fixture');
    }

    public function __toString()
    {
        return $this->venue;
    }
}
