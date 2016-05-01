<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Venue
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Fixture[] $fixtures
 * @mixin \Eloquent
 * @property integer $id
 * @property string $venue
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Venue whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Venue whereVenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Venue whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Venue whereUpdatedAt($value)
 */
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
