<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

abstract class Filter extends Component
{
    public string $label;

    public array|Collection $options;

    public string $selectedOption;

    public string $eventToEmit;

    public function render(): View
    {
        return view('livewire.filter');
    }

    protected function setOptions(Collection $options, bool $overwrite = false): void
    {
        $this->options = $options;
        if ($overwrite) {
            $this->selectedOption = $options->first()->getKey();
        } else {
            $this->selectedOption ??= $options->first()->getKey();
        }
    }

    #[On('option-selected')]
    public function emitEvent(): void
    {
        // This was initially accomplished by dispatching the even directly from
        // the component `filter.blade.php`.
        // However, although the selectedOption was added to the URL, it was not
        // stored in the browser's history (don't know why). There is a issue
        // which talks about something similar:
        // https://github.com/livewire/livewire/discussions/7583
        $this->dispatch($this->eventToEmit, $this->selectedOption);
    }

    abstract public static function buildQueryParam(string $value): array;

    abstract public static function getQueryParam(): ?string;
}
