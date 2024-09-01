<?php

namespace App\Livewire\Divisions;

use App\Livewire\Competitions\Filter as CompetitionsFilter;
use App\Livewire\Forms\DivisionForm;
use App\Livewire\Seasons\Filter as SeasonsFilter;
use App\Models\Division;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public DivisionForm $form;

    public function mount(Division $division): void
    {
        $this->form->creating = false;
        $this->form->setDivisionModel($division);
    }

    public function save(): void
    {
        $this->form->update();

        $this->redirectRoute(
            name: 'divisions.index',
            parameters: array_merge(
                SeasonsFilter::buildQueryParam($this->form->divisionModel->competition->season_id),
                CompetitionsFilter::buildQueryParam($this->form->divisionModel->competition_id)
            ),
            navigate: true
        );
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.division.edit');
    }
}
