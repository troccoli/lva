<?php

namespace App\Livewire\Clubs;

use App\Livewire\Forms\ClubForm;
use App\Models\Club;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public ClubForm $form;

    public function mount(Club $club): void
    {
        $this->form->setClubModel($club);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.club.show', [
            'club' => $this->form->clubModel,
        ]);
    }
}
