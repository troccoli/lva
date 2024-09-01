<?php

namespace App\Livewire\Seasons;

use App\Livewire\Forms\SeasonForm;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public SeasonForm $form;

    public function mount(): void
    {
        $this->form->creating = true;
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute('seasons.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.season.create');
    }
}
