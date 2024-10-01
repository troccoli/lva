<?php

namespace App\Livewire\Fixtures;

use App\Livewire\Competitions\Filter as CompetitionsFilter;
use App\Livewire\Divisions\Filter as DivisionsFilter;
use App\Livewire\Forms\FixtureForm;
use App\Livewire\Seasons\Filter as SeasonsFilter;
use App\Models\Division;
use App\Models\Season;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public FixtureForm $form;

    public function mount(): void
    {
        $this->form->creating = true;
        $this->form->division_id = request()->query('for');

        $division = Division::findOrFail($this->form->division_id);
        $this->form->divisionName = $division->name;
        $this->form->competitionName = $division->competition->name;
        $this->form->seasonName = $division->competition->season->name;
    }

    public function save(): void
    {
        $this->form->store();

        $seasonId = $this->form->fixtureModel->division->competition->season_id;
        $competitionId = $this->form->fixtureModel->division->competition_id;
        $divisionId = $this->form->fixtureModel->division_id;

        $this->redirectRoute(
            name: 'fixtures.index',
            parameters: array_merge(
                SeasonsFilter::buildQueryParam($seasonId),
                CompetitionsFilter::buildQueryParam($competitionId),
                DivisionsFilter::buildQueryParam($divisionId),
            ),
            navigate: true
        );
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $seasons = Season::latest('year')->get();
        $latestSeason = $seasons->first();
        $competitions = $latestSeason->competitions;

        return view('livewire.fixture.create', [
            'seasons' => $seasons,
            'competitions' => $competitions,
            'divisions' => $competitions->first()->divisions,
            'teams' => Team::all(),
            'venues' => Venue::query()->orderBy('name')->get(),
        ]);
    }
}
