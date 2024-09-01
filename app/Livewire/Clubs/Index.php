<?php

namespace App\Livewire\Clubs;

use App\Models\Club;
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
        $clubs = Club::query()
            ->orderBy('name')
            ->with('venue')
            ->simplePaginate(10);

        return view('livewire.club.index', [
            'clubs' => $clubs,
            'createUrl' => route('clubs.create'),
        ])
            ->with('i', $this->getPage() * $clubs->perPage());
    }

    public function delete(Club $club): void
    {
        $club->delete();

        $this->redirectRoute('clubs.index', navigate: true);
    }
}
