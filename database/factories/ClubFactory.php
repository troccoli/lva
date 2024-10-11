<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Club>
 */
class ClubFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->country(),
            'venue_id' => Venue::factory(),
        ];
    }

    public function withoutVenue(): static
    {
        return $this->state(fn (array $attributes) => [
            'venue_id' => null,
        ]);
    }

    public function at(Venue|VenueFactory|string $venue): static
    {
        return $this->state(fn (array $attributes) => [
            'venue_id' => $venue,
        ]);
    }
}
