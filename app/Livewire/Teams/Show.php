<?php

namespace App\Livewire\Teams;

use App\Livewire\Forms\TeamForm;
use App\Models\Team;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public TeamForm $form;

    public function mount(Team $team): void
    {
        $this->form->setTeamModel($team);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.team.show', [
            'team' => $this->form->teamModel,
        ]);
    }
}
