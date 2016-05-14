<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AvailableAppointment
 *
 * @property-read \App\Models\Fixture $fixture
 * @property-read \App\Models\Role $role
 * @mixin \Eloquent
 * @property integer $id
 * @property integer $fixture_id
 * @property integer $role_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AvailableAppointment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AvailableAppointment whereFixtureId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AvailableAppointment whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AvailableAppointment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AvailableAppointment whereUpdatedAt($value)
 */
class AvailableAppointment extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'available_appointments';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['fixture_id', 'role_id'];

    public function fixture()
    {
        return $this->belongsTo('App\Models\Fixture');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }
}
