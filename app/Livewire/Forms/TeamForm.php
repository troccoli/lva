<?php

namespace App\Livewire\Forms;

use App\Models\Team;
use Livewire\Form;

class TeamForm extends Form
{
    public ?Team $teamModel;

    public ?string $club_id;

    public ?string $clubName;

    public ?string $name;

    public ?string $venue_id;

    public function rules(): array
    {
        return [
            'club_id' => 'required|uuid|exists:clubs,id',
            'name' => 'required|string',
            'venue_id' => 'string|uuid|exists:venues,id',
        ];
    }

    public function setTeamModel(Team $teamModel): void
    {
        $this->teamModel = $teamModel;

        $this->club_id = $this->teamModel->club_id;
        $this->clubName = $this->teamModel->club?->name;
        $this->name = $this->teamModel->name;
        $this->venue_id = $this->teamModel->venue_id;
    }

    public function store(): void
    {
        $this->teamModel->create($this->validate());

        $this->reset();
    }

    public function update(): void
    {
        $this->teamModel->update($this->validate());

        $this->reset();
    }
}
