<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fixture>
 */
class FixtureFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'match_number' => fake()->unique()->randomNumber(),
            'division_id' => Division::factory(),
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'match_date' => fake()->date(),
            'start_time' => fake()->time(),
            'venue_id' => Venue::factory(),
        ];
    }

    public function number(int $matchNumber): static
    {
        return $this->state(fn (array $attributes) => [
            'match_number' => $matchNumber,
        ]);
    }

    public function inDivision(Division|DivisionFactory|string $division): static
    {
        return $this->state(fn (array $attributes) => [
            'division_id' => $division,
        ]);
    }

    public function between(Team|TeamFactory|string $homeTeam, Team|TeamFactory|string $awayTeam): static
    {
        return $this->state(fn (array $attributes) => [
            'home_team_id' => $homeTeam,
            'away_team_id' => $awayTeam,
        ]);
    }

    public function on(Carbon $date, ?Carbon $time = null): static
    {
        return $this->state(fn (array $attributes) => [
            'match_date' => $date,
            'start_time' => $time,
        ]);
    }

    public function at(Venue|VenueFactory|string $venue): static
    {
        return $this->state(fn (array $attributes) => [
            'venue_id' => $venue,
        ]);
    }
}
