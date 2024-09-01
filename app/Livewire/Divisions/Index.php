<?php

namespace App\Livewire\Divisions;

use App\Livewire\Competitions\Filter as CompetitionsFilter;
use App\Livewire\Seasons\Filter as SeasonsFilter;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $competitionId;

    public function mount(): void
    {
        $seasons = Season::latest('year')->get();
        $latestSeason = $seasons->first();
        $competitions = $latestSeason->competitions;
        $this->competitionId = $competitions->first()->getKey();
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $divisions = Division::query()
            ->inCompetition($this->competitionId)
            ->with(['competition', 'competition.season'])
            ->oldest('display_order')
            ->simplePaginate(10);

        return view('livewire.division.index', [
            'divisions' => $divisions,
            'createUrl' => route('divisions.create', ['for' => $this->competitionId]),
        ])
            ->with('i', $this->getPage() * $divisions->perPage());
    }

    public function delete(Division $division): void
    {
        $division->delete();

        $this->redirectRoute(
            name: 'divisions.index',
            parameters: array_merge(
                SeasonsFilter::buildQueryParam(Competition::query()->whereKey($this->competitionId)->first()->season_id),
                CompetitionsFilter::buildQueryParam($this->competitionId)
            ),
            navigate: true
        );
    }

    #[On('competition-selected')]
    public function setCurrentCompetition($competitionId): void
    {
        $this->competitionId = $competitionId;
    }
}
