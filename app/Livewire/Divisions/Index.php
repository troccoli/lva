<?php

namespace App\Livewire\Divisions;

use App\Models\Division;
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
        $divisions = Division::with(['competition', 'competition.season'])
            ->oldest('display_order')
            ->simplePaginate(10);

        return view('livewire.division.index', compact('divisions'))
            ->with('i', $this->getPage() * $divisions->perPage());
    }

    public function delete(Division $division): void
    {
        $division->delete();

        $this->redirectRoute('divisions.index', navigate: true);
    }
}
