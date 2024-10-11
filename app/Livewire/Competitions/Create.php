<?php

namespace App\Livewire\Competitions;

use App\Livewire\Forms\CompetitionForm;
use App\Livewire\Seasons\Filter as SeasonsFilter;
use App\Models\Season;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public CompetitionForm $form;

    public function mount(): void
    {
        $this->form->creating = true;
        $this->form->season_id = request()->query('for');

        $this->form->seasonName = Season::findOrFail($this->form->season_id)->name;
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute(
            name: 'competitions.index',
            parameters: SeasonsFilter::buildQueryParam($this->form->competitionModel->season_id),
            navigate: true,
        );
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.competition.create', [
            'seasons' => Season::latest('year')->get(),
        ]);
    }
}
