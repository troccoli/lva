<?php

namespace App\Livewire\Competitions;

use App\Models\Competition;
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
        $competitions = Competition::with('season')
            ->simplePaginate(10);

        return view('livewire.competition.index', compact('competitions'))
            ->with('i', $this->getPage() * $competitions->perPage());
    }

    public function delete(Competition $competition): void
    {
        $competition->delete();

        $this->redirectRoute('competitions.index', navigate: true);
    }
}
