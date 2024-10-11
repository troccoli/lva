<?php

namespace App\Livewire\Competitions;

use App\Livewire\Forms\CompetitionForm;
use App\Models\Competition;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public CompetitionForm $form;

    public function mount(Competition $competition): void
    {
        $this->form->setCompetitionModel($competition);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.competition.show', [
            'competition' => $this->form->competitionModel,
        ]);
    }
}
