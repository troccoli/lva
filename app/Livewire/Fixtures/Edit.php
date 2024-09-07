<?php

namespace App\Livewire\Fixtures;

use App\Livewire\Forms\FixtureForm;
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
        $this->form->setFixtureModel($fixture);
    }

    public function save(): void
    {
        $this->form->update();

        $this->redirectRoute('fixtures.index', navigate: true);
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
