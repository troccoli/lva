<?php

namespace App\Livewire\Seasons;

use App\Livewire\Filter as BaseFilter;
use App\Models\Season;
use Livewire\Attributes\Url;

class Filter extends BaseFilter
{
    #[Url(as: 'se', history: true)]
    public string $selectedOption;

    public function mount(): void
    {
        $seasons = Season::latest('year')->get();

        $this->setOptions($seasons);
        $this->label = 'Seasons';
        $this->eventToEmit = 'season-selected';

        $this->dispatch($this->eventToEmit, $this->selectedOption);
    }

    public static function buildQueryParam(string $value): array
    {
        return ['se' => $value];
    }

    public static function getQueryParam(): ?string
    {
        return request()->query('se');
    }
}
