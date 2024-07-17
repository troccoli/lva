<?php

namespace Database\Factories;

use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Division>
 */
class DivisionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'name' => fake()->unique()->company(),
            'display_order' => fake()->unique()->numberBetween(1, 100),
        ];
    }

    public function inCompetition(Competition|CompetitionFactory|string $competition): static
    {
        return $this->state(fn (array $attributes) => [
            'competition_id' => $competition,
        ]);
    }
}
