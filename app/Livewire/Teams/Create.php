<?php

namespace App\Livewire\Teams;

use App\Livewire\Clubs\Filter as ClubFilter;
use App\Livewire\Forms\TeamForm;
use App\Models\Club;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public TeamForm $form;

    public function mount(): void
    {
        $this->form->creating = true;
        $this->form->club_id = request()->query('for');

        $this->form->clubName = Club::findOrFail($this->form->club_id)->name;
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute(
            name: 'teams.index',
            parameters: ClubFilter::buildQueryParam($this->form->teamModel->club_id),
            navigate: true,
        );
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
