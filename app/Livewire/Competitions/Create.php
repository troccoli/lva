<?php

namespace App\Livewire\Competitions;

use App\Livewire\Forms\CompetitionForm;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public CompetitionForm $form;

    public function mount(Competition $competition): void
    {
        $this->form->setCompetitionModel($competition);
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute('competitions.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.competition.create', ['seasons' => Season::latest('year')->get()]);
    }
}
