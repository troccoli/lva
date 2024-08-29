<?php

namespace App\Livewire\Divisions;

use App\Livewire\Forms\DivisionForm;
use App\Models\Division;
use App\Models\Season;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public DivisionForm $form;

    public function mount(Division $division): void
    {
        $this->form->setDivisionModel($division);
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute('divisions.index', navigate: true);
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
