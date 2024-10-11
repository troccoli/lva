<?php

namespace App\Livewire\Seasons;

use App\Livewire\Forms\SeasonForm;
use App\Models\Season;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public SeasonForm $form;

    public function mount(Season $season): void
    {
        $this->form->setSeasonModel($season);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.season.show', [
            'season' => $this->form->seasonModel,
        ]);
    }
}
