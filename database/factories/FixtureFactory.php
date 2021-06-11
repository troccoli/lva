<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class FixtureFactory extends Factory
{
    protected $model = Fixture::class;

    public function definition(): array
    {
        return [
            'match_number' => $this->faker->unique()->randomNumber(),
            'division_id' => Division::factory(),
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'match_date' => $this->faker->date(),
            'match_time' => $this->faker->time(),
            'venue_id' => Venue::factory(),
        ];
    }

    public function number(int $matchNumber): self
    {
        return $this->state(function (array $attributes) use ($matchNumber) {
            return [
                'match_number' => $matchNumber,
            ];
        });
    }

    public function inDivision(Division $division): self
    {
        return $this->state(function (array $attributes) use ($division) {
            return [
                'division_id' => $division->getId(),
            ];
        });
    }

    public function between(Team $homeTeam, Team $awayTeam): self
    {
        return $this->state(function (array $attributes) use ($homeTeam, $awayTeam) {
            return [
                'home_team_id' => $homeTeam->getId(),
                'away_team_id' => $awayTeam->getId(),
            ];
        });
    }

    public function on(Carbon $date, ?Carbon $time = null): self
    {
        return $this->state(function (array $attributes) use ($date, $time) {
            $data = [
                'match_date' => $date,
            ];
            if ($time) {
                $data['match_time'] = $time;
            }

            return $data;
        });
    }

    public function at(Venue $venue): self
    {
        return $this->state(function (array $attributes) use ($venue) {
            return [
                'venue_id' => $venue->getId(),
            ];
        });
    }
}
