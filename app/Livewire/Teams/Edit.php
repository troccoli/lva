<?php

namespace App\Livewire\Teams;

use App\Livewire\Clubs\Filter as ClubFilter;
use App\Livewire\Forms\TeamForm;
use App\Models\Club;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public TeamForm $form;

    public function mount(Team $team): void
    {
        $this->form->creating = false;
        $this->form->setTeamModel($team);
    }

    public function save(): void
    {
        $this->form->update();

        $this->redirectRoute(
            name: 'teams.index',
            parameters: ClubFilter::buildQueryParam($this->form->teamModel->club_id),
            navigate: true
        );
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.team.edit', [
            'clubs' => Club::query()->orderBy('name')->get(),
            'venues' => Venue::query()->orderBy('name')->get(),
        ]);
    }
}
