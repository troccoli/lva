<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'divisions';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['season_id', 'division'];

}
