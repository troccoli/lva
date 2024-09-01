<?php

namespace App\Livewire\Venues;

use App\Livewire\Forms\VenueForm;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public VenueForm $form;

    public function mount(Venue $venue): void
    {
        $this->form->setVenueModel($venue);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.venue.show', [
            'venue' => $this->form->venueModel]
        );
    }
}
