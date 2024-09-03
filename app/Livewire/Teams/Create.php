<?php

namespace App\Livewire\Teams;

use App\Livewire\Forms\TeamForm;
use App\Models\Club;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public TeamForm $form;

    public function mount(Team $team): void
    {
        $this->form->setTeamModel($team);
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute('teams.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.team.create', [
            'clubs' => Club::query()->orderBy('name')->get(),
            'venues' => Venue::query()->orderBy('name')->get(),
        ]);
    }
}
