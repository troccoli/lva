<?php

namespace App\Livewire\Clubs;

use App\Livewire\Forms\ClubForm;
use App\Models\Club;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Edit extends Component
{
    public ClubForm $form;

    public function mount(Club $club): void
    {
        $this->form->creating = false;
        $this->form->setClubModel($club);
    }

    public function save(): void
    {
        $this->form->update();

        $this->redirectRoute('clubs.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.club.edit', [
            'venues' => Venue::query()->orderBy('name')->get(),
        ]);
    }
}
