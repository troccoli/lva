<?php

namespace App\Livewire\Forms;

use App\Models\Season;
use Livewire\Form;

class SeasonForm extends Form
{
    public ?Season $seasonModel;

    public ?int $year = null;

    public bool $creating = true;

    public function rules(): array
    {
        return [
            'year' => 'required|numeric|digits:4',
        ];
    }

    public function setSeasonModel(Season $seasonModel): void
    {
        $this->seasonModel = $seasonModel;

        $this->year = $this->seasonModel->year;
    }

    public function store(): void
    {
        $this->seasonModel = Season::create($this->validate());

        $this->resetExcept('seasonModel');
    }

    public function update(): void
    {
        $this->seasonModel->update($this->validate());
        $this->seasonModel->refresh();

        $this->resetExcept('seasonModel');
    }
}
