<?php

namespace App\Livewire\Forms;

use App\Models\Competition;
use Livewire\Form;

class CompetitionForm extends Form
{
    public ?Competition $competitionModel;

    public ?string $season_id;

    public ?string $seasonName;

    public ?string $name;

    public bool $creating = true;

    public function rules(): array
    {
        return [
            'season_id' => 'required|uuid|exists:seasons,id',
            'name' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'season_id.required' => 'Please choose a season.',
            'name.required' => 'The competition\'s name is required.',
        ];
    }

    public function setCompetitionModel(Competition $competitionModel): void
    {
        $this->competitionModel = $competitionModel;

        $this->seasonName = $this->competitionModel->season->name;
        $this->season_id = $this->competitionModel->season_id;
        $this->name = $this->competitionModel->name;
    }

    public function store(): void
    {
        $this->competitionModel = Competition::create($this->validate());

        $this->resetExcept('competitionModel');
    }

    public function update(): void
    {
        $this->competitionModel->update($this->validate());
        $this->competitionModel->refresh();

        $this->resetExcept('competitionModel');
    }
}
