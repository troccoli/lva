<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = ['year'];

    public function getId(): int
    {
        return $this->id;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getName(): string
    {
        return sprintf('%4u/%02u', $this->year, ($this->year + 1) % 100);
    }
}
