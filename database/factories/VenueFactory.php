<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->uuid(),
            'name' => Str::replace("\n", ' ', $this->faker->unique()->address()),
        ];
    }
}
