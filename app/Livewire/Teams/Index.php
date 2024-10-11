<?php

namespace App\Livewire\Teams;

use App\Livewire\Clubs\Filter as ClubFilter;
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

    public function mount(): void
    {
        $clubs = Club::query()->orderBy('name')->get();
        $firstClub = $clubs->first();
        $this->clubId = $firstClub->getKey();
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $teams = Team::query()
            ->where('club_id', $this->clubId)
            ->with(['club', 'venue'])
            ->orderBy('name')
            ->simplePaginate(10);

        return view('livewire.team.index', [
            'teams' => $teams,
            'createUrl' => route('teams.create', ['for' => $this->clubId]),
        ])
            ->with('i', $this->getPage() * $teams->perPage());
    }

    public function delete(Team $team): void
    {
        $team->delete();

        $this->redirectRoute(
            name: 'teams.index',
            parameters: ClubFilter::buildQueryParam($this->clubId),
            navigate: true
        );
    }

    #[On('club-selected')]
    public function setCurrentClub($clubId): void
    {
        $this->clubId = $clubId;
    }
}
