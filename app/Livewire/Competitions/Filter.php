<?php

namespace App\Livewire\Competitions;

use App\Livewire\Filter as BaseFilter;
use App\Livewire\Seasons\Filter as SeasonsFilter;
use App\Models\Season;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class Filter extends BaseFilter
{
    #[Url(as: 'co', history: true)]
    public string $selectedOption;

    public function mount(): void
    {
        if (SeasonsFilter::getQueryParam()) {
            $selectedSeason = Season::findOrFail(SeasonsFilter::getQueryParam());
        } else {
            $seasons = Season::latest('year')->get();
            $selectedSeason = $seasons->first();
        }

        $this->label = 'Competitions';
        $this->eventToEmit = 'competition-selected';

        $this->setOptions($selectedSeason->competitions);
        $this->dispatch($this->eventToEmit, $this->selectedOption);
    }

    #[On('season-selected')]
    public function updateFilter($seasonId): void
    {
        $selectedSeason = Season::findOrFail($seasonId);

        $this->setOptions($selectedSeason->competitions, overwrite: true);
        $this->dispatch($this->eventToEmit, $this->selectedOption);
    }

    public static function buildQueryParam(string $value): array
    {
        return ['co' => $value];
    }

    public static function getQueryParam(): ?string
    {
        return request()->query('co');
    }
}
