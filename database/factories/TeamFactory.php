<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'name' => $this->faker->unique()->city(),
            'venue_id' => null,
        ];
    }
}
