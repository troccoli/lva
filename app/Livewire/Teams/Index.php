<?php

namespace App\Livewire\Teams;

use App\Models\Club;
use App\Models\Team;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $clubId;

    public array $filters;

    public function mount(): void
    {
        $clubs = Club::query()->orderBy('name')->get();
        $this->clubId = $clubs->first()->getKey();

        $this->filters = [
            'clubs' => [
                'label' => 'Clubs',
                'options' => $clubs,
                'currentOption' => $this->clubId,
                'event' => 'club-selected',
            ],
        ];
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $teams = Team::query()
            ->where('club_id', $this->clubId)
            ->with(['club', 'venue'])
            ->orderBy('name')
            ->simplePaginate(10);

        return view('livewire.team.index', compact('teams'))
            ->with('i', $this->getPage() * $teams->perPage());
    }

    public function delete(Team $team): void
    {
        $team->delete();

        $this->redirectRoute('teams.index', navigate: true);
    }

    #[On('club-selected')]
    public function setCurrentClub($clubId): void
    {
        $this->clubId = $clubId;
    }
}
