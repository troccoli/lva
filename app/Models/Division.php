<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Division
 * @package App\Models
 */
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

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function fixtures()
    {
        return $this->hasMany(Fixture::class);
    }

    public function __toString()
    {
        return $this->season . ' ' . $this->division;
    }
}
