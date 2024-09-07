<?php

namespace App\Livewire\Forms;

use App\Models\Fixture;
use Livewire\Form;

class FixtureForm extends Form
{
    public ?Fixture $fixtureModel;

    public ?int $match_number;

    public ?string $division_id;

    public ?string $home_team_id;

    public ?string $away_team_id;

    public $match_date = '';

    public $start_time = '';

    public ?string $venue_id;

    public ?string $seasonName;

    public ?string $competitionName;

    public ?string $divisionName;

    public function rules(): array
    {
        return [
            'match_number' => 'required|int|min:1',
            'division_id' => 'required|uuid|exists:divisions,id',
            'home_team_id' => 'required|uuid|exists:teams,id',
            'away_team_id' => 'required|uuid|exists:teams,id',
            'match_date' => 'required|date',
            'start_time' => 'required|time',
            'venue_id' => 'required|uuid|exists:venues,id',
        ];
    }

    public function setFixtureModel(Fixture $fixtureModel): void
    {
        $this->fixtureModel = $fixtureModel;

        $this->match_number = $this->fixtureModel->match_number;
        $this->division_id = $this->fixtureModel->division_id;
        $this->home_team_id = $this->fixtureModel->home_team_id;
        $this->away_team_id = $this->fixtureModel->away_team_id;
        $this->match_date = $this->fixtureModel->match_date;
        $this->venue_id = $this->fixtureModel->venue_id;
        $this->seasonName = $this->fixtureModel->division?->competition?->season?->name;
        $this->competitionName = $this->fixtureModel->division?->competition?->name;
        $this->divisionName = $this->fixtureModel->division?->name;
    }

    public function store(): void
    {
        $this->fixtureModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->fixtureModel->update($this->validate());

        $this->reset();
    }
}
