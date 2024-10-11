<?php

namespace Database\Factories;

use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competition>
 */
class CompetitionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'name' => fake()->unique()->company(),
        ];
    }

    public function inSeason(Season|SeasonFactory|string $season): static
    {
        return $this->state(fn (array $attributes) => [
            'season_id' => $season,
        ]);
    }
}
