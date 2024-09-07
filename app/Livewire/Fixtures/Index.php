<?php

namespace App\Livewire\Fixtures;

use App\Models\Competition;
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

    public array $filters;

    public function mount(): void
    {
        $seasons = Season::latest('year')->get();
        $currentSeason = $seasons->first();
        $competitions = $currentSeason->competitions;
        $currentCompetition = $competitions->first();
        $divisions = $currentCompetition->divisions;
        $this->divisionId = $divisions->first()->getKey();

        $this->filters = [
            'seasons' => [
                'label' => 'Seasons',
                'options' => $seasons,
                'currentOption' => $currentSeason->getKey(),
                'event' => 'season-selected',
            ],
            'competitions' => [
                'label' => 'Competitions',
                'options' => $competitions,
                'currentOption' => $currentCompetition->getKey(),
                'event' => 'competition-selected',
            ],
            'divisions' => [
                'label' => 'Divisions',
                'options' => $divisions,
                'currentOption' => $this->divisionId,
                'event' => 'division-selected',
            ],
        ];
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $fixtures = Fixture::query()
            ->where('division_id', $this->divisionId)
            ->with('division', 'division.competition', 'division.competition.season', 'venue')
            ->oldest('match_number')
            ->simplePaginate(10);

        return view('livewire.fixture.index', compact('fixtures'))
            ->with('i', $this->getPage() * $fixtures->perPage());
    }

    public function delete(Fixture $fixture): void
    {
        $fixture->delete();

        $this->redirectRoute('fixtures.index', navigate: true);
    }

    #[On('season-selected')]
    public function updateCompetitions($seasonId): void
    {
        $selectedSeason = Season::findOrFail($seasonId);
        $competitions = $selectedSeason->competitions;
        $selectedCompetition = $competitions->first();
        $divisions = $selectedCompetition->divisions;
        $this->divisionId = $divisions->first()->getKey();
        $this->filters['competitions']['options'] = $competitions;
        $this->filters['competitions']['currentOption'] = $selectedCompetition->getKey();
        $this->filters['divisions']['options'] = $divisions;
        $this->filters['divisions']['currentOption'] = $this->divisionId;
    }

    #[On('competition-selected')]
    public function setCurrentCompetition($competitionId): void
    {
        $selectedCompetition = Competition::findOrFail($competitionId);
        $divisions = $selectedCompetition->divisions;
        $this->divisionId = $divisions->first()->getKey();
        $this->filters['divisions']['options'] = $divisions;
        $this->filters['divisions']['currentOption'] = $this->divisionId;
    }

    #[On('division-selected')]
    public function setCurrentDivision($divisionId): void
    {
        $this->divisionId = $divisionId;
    }
}
