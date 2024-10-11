<?php

namespace App\Livewire\Fixtures;

use App\Livewire\Forms\FixtureForm;
use App\Models\Fixture;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public FixtureForm $form;

    public function mount(Fixture $fixture): void
    {
        $this->form->setFixtureModel($fixture);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.fixture.show', [
            'fixture' => $this->form->fixtureModel,
        ]);
    }
}
