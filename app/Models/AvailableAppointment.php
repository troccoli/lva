<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AvailableAppointment
 * @package App\Models
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
        return $this->belongsTo(Fixture::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
