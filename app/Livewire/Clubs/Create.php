<?php

namespace App\Livewire\Clubs;

use App\Livewire\Forms\ClubForm;
use App\Models\Venue;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Create extends Component
{
    public ClubForm $form;

    public function mount(): void
    {
        $this->form->creating = true;
    }

    public function save(): void
    {
        $this->form->store();

        $this->redirectRoute('clubs.index', navigate: true);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        return view('livewire.club.create', [
            'venues' => Venue::query()->orderBy('name')->get(),
        ]);
    }
}
