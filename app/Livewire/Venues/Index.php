<?php

namespace App\Livewire\Venues;

use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    public function render(): View
    {
        $venues = Venue::simplePaginate(10);

        return view('livewire.venue.index', [
            'venues' => $venues,
            'createUrl' => route('venues.create'),
        ])
            ->with('i', $this->getPage() * $venues->perPage());
    }

    public function delete(Venue $venue): void
    {
        $venue->delete();

        $this->redirectRoute('venues.index', navigate: true);
    }
}
