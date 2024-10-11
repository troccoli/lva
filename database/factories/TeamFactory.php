<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'name' => fake()->unique()->city(),
            'venue_id' => Venue::factory(),
        ];
    }

    public function withoutVenue(): static
    {
        return $this->state([
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
