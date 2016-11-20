<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @package App\Models
 */
class Role extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['role'];

    public function available_appointments()
    {
        return $this->hasMany(AvailableAppointment::class);
    }

    public function __toString()
    {
        return $this->role;
    }
}
