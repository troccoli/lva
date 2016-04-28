<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function available_appointment()
    {
        return $this->hasMany('App\Models\AvailableAppointment');
    }

    public function __toString()
    {
        return $this->role;
    }
}
