<?php

namespace App\Livewire\Forms;

use App\Models\Division;
use Livewire\Form;

class DivisionForm extends Form
{
    public ?Division $divisionModel;

    public ?string $competition_id;

    public ?string $name;

    public ?int $display_order;

    public ?string $seasonName;

    public ?string $competitionName;

    public bool $creating = true;

    public function rules(): array
    {
        return [
            'competition_id' => 'required|uuid|exists:competitions,id',
            'name' => 'required|string',
            'display_order' => 'required|int|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'competition_id.required' => 'Please choose a competition.',
            'name.required' => 'The division\'s name is required.',
            'display_order.required' => 'Please choose a positive number.',
        ];
    }

    public function setDivisionModel(Division $divisionModel): void
    {
        $this->divisionModel = $divisionModel;

        $this->competition_id = $this->divisionModel->competition_id;
        $this->name = $this->divisionModel->name;
        $this->display_order = $this->divisionModel->display_order;
        $this->seasonName = $this->divisionModel->competition?->season?->name;
        $this->competitionName = $this->divisionModel->competition?->name;

    }

    public function store(): void
    {
        $this->divisionModel = Division::create($this->validate());

        $this->resetExcept('divisionModel');
    }

    public function update(): void
    {
        $this->divisionModel->update($this->validate());
        $this->divisionModel->refresh();

        $this->resetExcept('divisionModel');
    }
}
