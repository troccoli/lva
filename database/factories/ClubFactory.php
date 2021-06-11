<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClubFactory extends Factory
{
    protected $model = Club::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->state(),
            'venue_id' => Venue::factory(),
        ];
    }

    public function withoutVenue()
    {
        return $this->state(function (array $attributes): array {
            return [
                'venue_id' => null,
            ];
        });
    }
}
