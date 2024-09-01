<?php

namespace App\Livewire\Divisions;

use App\Livewire\Forms\DivisionForm;
use App\Models\Division;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public DivisionForm $form;

    public function mount(Division $division): void
    {
        $this->form->setDivisionModel($division);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.division.show', [
            'division' => $this->form->divisionModel,
        ]);
    }
}
