<?php

namespace App\Livewire\Divisions;

use App\Livewire\Competitions\Filter as CompetitionsFilter;
use App\Livewire\Forms\DivisionForm;
use App\Livewire\Seasons\Filter as SeasonsFilter;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public DivisionForm $form;

    public function mount(): void
    {
        $this->form->creating = true;
        $this->form->competition_id = request()->query('for');

        $competition = Competition::findOrFail($this->form->competition_id);
        $this->form->competitionName = $competition->name;
        $this->form->seasonName = $competition->season->name;
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute(
            name: 'divisions.index',
            parameters: array_merge(
                SeasonsFilter::buildQueryParam($this->form->divisionModel->competition->season_id),
                CompetitionsFilter::buildQueryParam($this->form->divisionModel->competition_id),
            ),
            navigate: true,
        );
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $seasons = Season::latest('year')->get();

        return view('livewire.division.create', [
            'seasons' => $seasons,
            'competitions' => $seasons->first()->competitions,
        ]);
    }
}
