<?php

namespace App\Livewire\Divisions;

use App\Livewire\Competitions\Filter as CompetitionsFilter;
use App\Livewire\Filter as BaseFilter;
use App\Models\Competition;
use App\Models\Season;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class Filter extends BaseFilter
{
    #[Url(as: 'di')]
    public string $selectedOption;

    public function mount(): void
    {
        if (CompetitionsFilter::getQueryParam()) {
            $selectedCompetition = Competition::findOrFail(CompetitionsFilter::getQueryParam());
        } else {
            $seasons = Season::latest('year')->get();
            $latestSeason = $seasons->first();
            $competitions = $latestSeason->competitions;
            $selectedCompetition = $competitions->first();
        }

        $this->setOptions($selectedCompetition->divisions);
        $this->label = 'Divisions';
        $this->eventToEmit = 'division-selected';

        $this->dispatch($this->eventToEmit, $this->selectedOption);
    }

    #[On('competition-selected')]
    public function updateFilter($competitionId): void
    {
        $selectedCompetition = Competition::findOrFail($competitionId);
        $this->setOptions($selectedCompetition->divisions, overwrite: true);
        $this->dispatch($this->eventToEmit, $this->selectedOption);
    }

    public static function buildQueryParam(string $value): array
    {
        return ['di' => $value];
    }

    public static function getQueryParam(): ?string
    {
        return request()->query('di');
    }
}
