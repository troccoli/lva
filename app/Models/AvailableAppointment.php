<?php

namespace LVA\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AvailableAppointment.
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
