<?php

namespace App\Livewire\Venues;

use App\Livewire\Forms\VenueForm;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public VenueForm $form;

    public function mount(Venue $venue): void
    {
        $this->form->creating = false;
        $this->form->setVenueModel($venue);
    }

    public function save(): void
    {
        $this->form->update();

        $this->redirectRoute('venues.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.venue.edit');
    }
}
