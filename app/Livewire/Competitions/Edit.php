<?php

namespace App\Livewire\Competitions;

use App\Livewire\Forms\CompetitionForm;
use App\Livewire\Seasons\Filter as SeasonsFilter;
use App\Models\Competition;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public CompetitionForm $form;

    public function mount(Competition $competition): void
    {
        $this->form->creating = false;
        $this->form->setCompetitionModel($competition);
    }

    public function save(): void
    {
        $this->form->update();

        $this->redirectRoute(
            name: 'competitions.index',
            parameters: SeasonsFilter::buildQueryParam($this->form->competitionModel->season_id),
            navigate: true
        );
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.competition.edit');
    }
}
