<?php

namespace App\Livewire\Forms;

use App\Models\Venue;
use Livewire\Form;

class VenueForm extends Form
{
    public ?Venue $venueModel;

    public ?string $name;

    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

    public function setVenueModel(Venue $venueModel): void
    {
        $this->venueModel = $venueModel;

        $this->name = $this->venueModel->name;
    }

    public function store(): void
    {
        $this->venueModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->venueModel->update($this->validate());

        $this->reset();
    }
}
