<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'name' => $this->faker->unique()->company(),
        ];
    }
}
