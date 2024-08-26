<?php

namespace App\Livewire\Seasons;

use App\Models\Season;
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
        $seasons = Season::latest('year')->simplePaginate(10);

        return view('livewire.season.index', compact('seasons'))
            ->with('i', $this->getPage() * $seasons->perPage());
    }

    public function delete(Season $season): void
    {
        $season->delete();

        $this->redirectRoute('seasons.index', navigate: true);
    }
}
