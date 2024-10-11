<?php

namespace App\Livewire\Clubs;

use App\Livewire\Filter as BaseFilter;
use App\Models\Club;
use Livewire\Attributes\Url;

class Filter extends BaseFilter
{
    #[Url(as: 'cl', history: true)]
    public string $selectedOption;

    public function mount(): void
    {
        $clubs = Club::query()->orderBy('name')->get();

        $this->setOptions($clubs);
        $this->label = 'Clubs';
        $this->eventToEmit = 'club-selected';

        $this->dispatch($this->eventToEmit, $this->selectedOption);
    }

    public static function buildQueryParam(string $value): array
    {
        return ['cl' => $value];
    }

    public static function getQueryParam(): ?string
    {
        return request()->query('cl');
    }
}
