<?php

namespace App\Livewire\Fixtures;

use App\Livewire\Forms\FixtureForm;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public FixtureForm $form;

    public function mount(Fixture $fixture): void
    {
        $this->form->setFixtureModel($fixture);
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute('fixtures.index', navigate: true);
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
