<?php

namespace App\Livewire\Venues;

use App\Livewire\Forms\VenueForm;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public VenueForm $form;

    public function mount(): void
    {
        $this->form->creating = true;
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute('venues.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.venue.create');
    }
}
