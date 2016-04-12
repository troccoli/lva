<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}
