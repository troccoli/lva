<?php

namespace App\Livewire\Competitions;

use App\Livewire\Seasons\Filter as SeasonFilter;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $seasonId;

    public function mount(): void
    {
        $seasons = Season::latest('year')->get();
        $latestSeason = $seasons->first();
        $this->seasonId = $latestSeason->getKey();
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $competitions = Competition::query()
            ->inSeason($this->seasonId)
            ->with('season')
            ->latest()
            ->simplePaginate(10);

        return view('livewire.competition.index', [
            'competitions' => $competitions,
            'createUrl' => route('competitions.create', ['for' => $this->seasonId]),
        ])
            ->with('i', $this->getPage() * $competitions->perPage());
    }

    public function delete(Competition $competition): void
    {
        $competition->delete();

        $this->redirectRoute(
            name: 'competitions.index',
            parameters: SeasonFilter::buildQueryParam($this->seasonId),
            navigate: true,
        );
    }

    #[On('season-selected')]
    public function setCurrentSeason($seasonId): void
    {
        $this->seasonId = $seasonId;
    }
}
