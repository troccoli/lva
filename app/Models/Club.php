<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['name'];

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
