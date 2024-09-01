<?php

namespace App\Livewire\Fixtures;

use App\Livewire\Competitions\Filter as CompetitionsFilter;
use App\Livewire\Divisions\Filter as DivisionsFilter;
use App\Livewire\Seasons\Filter as SeasonsFilter;
use App\Models\Fixture;
use App\Models\Season;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $divisionId;

    public function mount(): void
    {
        $seasons = Season::latest('year')->get();
        $latestSeason = $seasons->first();
        $competitions = $latestSeason->competitions;
        $firstCompetition = $competitions->first();
        $divisions = $firstCompetition->divisions;
        $this->divisionId = $divisions->first()->getKey();
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $fixtures = Fixture::query()
            ->where('division_id', $this->divisionId)
            ->with('division', 'division.competition', 'division.competition.season', 'venue')
            ->oldest('match_number')
            ->simplePaginate(10);

        return view('livewire.fixture.index', [
            'fixtures' => $fixtures,
            'createUrl' => route('fixtures.create', ['for' => $this->divisionId]),
        ])
            ->with('i', $this->getPage() * $fixtures->perPage());
    }

    public function delete(Fixture $fixture): void
    {
        $seasonId = $fixture->division->competition->season_id;
        $competitionId = $fixture->division->competition_id;
        $divisionId = $fixture->division_id;

        $fixture->delete();

        $this->redirectRoute(
            name: 'divisions.index',
            parameters: array_merge(
                SeasonsFilter::buildQueryParam($seasonId),
                CompetitionsFilter::buildQueryParam($competitionId),
                DivisionsFilter::buildQueryParam($divisionId),
            ),
            navigate: true
        );
    }

    #[On('division-selected')]
    public function setCurrentDivision($divisionId): void
    {
        $this->divisionId = $divisionId;
    }
}
