<?php

namespace App\Livewire\Forms;

use App\Models\Club;
use Livewire\Form;

class ClubForm extends Form
{
    public ?Club $clubModel;

    public ?string $name;

    public ?string $venue_id;

    public bool $creating = true;

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'venue_id' => 'string|uuid|exists:venues,id',
        ];
    }

    public function setClubModel(Club $clubModel): void
    {
        $this->clubModel = $clubModel;

        $this->name = $this->clubModel->name;
        $this->venue_id = $this->clubModel->venue_id;
    }

    public function store(): void
    {
        $this->clubModel = Club::create($this->validate());

        $this->resetExcept('clubModel');
    }

    public function update(): void
    {
        $this->clubModel->update($this->validate());

        $this->reset();
    }
}
