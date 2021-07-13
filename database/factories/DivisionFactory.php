<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

class DivisionFactory extends Factory
{
    protected $model = Division::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'name' => $this->faker->unique()->company(),
            'display_order' => $this->faker->unique()->numberBetween(1, 1000),
        ];
    }
}
