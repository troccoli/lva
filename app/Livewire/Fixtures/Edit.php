<?php

namespace App\Livewire\Fixtures;

use App\Livewire\Competitions\Filter as CompetitionsFilter;
use App\Livewire\Divisions\Filter as DivisionsFilter;
use App\Livewire\Forms\FixtureForm;
use App\Livewire\Seasons\Filter as SeasonsFilter;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public FixtureForm $form;

    public function mount(Fixture $fixture): void
    {
        $this->form->creating = false;
        $this->form->setFixtureModel($fixture);
    }

    public function save(): void
    {
        $this->form->update();

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
        return view('livewire.fixture.edit', [
            'teams' => Team::all(),
            'venues' => Venue::query()->orderBy('name')->get(),
        ]);
    }
}
