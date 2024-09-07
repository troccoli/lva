<div class="space-y-6">
    <div>
        <x-input-label for="season_id" value="Season" />
        @empty($form->division_id)
            <x-select-input wire:model="form.season_id" id="season_id" name="season_id" class="mt-1 block w-full"
                            :options="$seasons" placeholder="Choose a season" />
        @else
            <x-text-input wire:model="form.seasonName" id="season_id" name="season_id" type="text"
                          class="mt-1 block w-full" disabled />
        @endempty
        @error('form.season_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input-label for="competitions_id" value="Competition" />
        @empty($form->division_id)
            <x-select-input wire:model="form.competition_id" id="competition_id" name="competition_id"
                            class="mt-1 block w-full"
                            :options="$competitions" placeholder="Choose a competition" />
        @else
            <x-text-input wire:model="form.competitionName" id="competition_id" name="competition_id" type="text"
                          class="mt-1 block w-full" disabled />
        @endempty
        @error('form.competition_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input-label for="division_id" value="Division" />
        @empty($form->division_id)
            <x-select-input wire:model="form.division_id" id="division_id" name="division_id"
                            class="mt-1 block w-full"
                            :options="$divisions" placeholder="Choose a division" />
        @else
            <x-text-input wire:model="form.divisionName" id="division_id" name="division_id" type="text"
                          class="mt-1 block w-full" disabled />
        @endempty
        @error('form.division_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input-label for="match_number" value="Match #" />
        <x-text-input wire:model="form.match_number" id="match_number" name="match_number" type="text" class="mt-1 block w-full"
                      autocomplete="match_number" placeholder="Match number" />
        @error('form.match_number')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input-label for="home_team_id" value="Home team" />
        <x-select-input wire:model="form.home_team_id" id="home_team_id" name="home_team_id"
                        class="mt-1 block w-full"
                        :options="$teams" placeholder="Choose a team" />
        @error('form.home_team_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input-label for="away_team_id" value="Away team" />
        <x-select-input wire:model="form.away_team_id" id="away_team_id" name="away_team_id"
                        class="mt-1 block w-full"
                        :options="$teams" placeholder="Choose a team" />
        @error('form.away_team_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input-label for="match_date" value="Date" />
        <x-text-input
            type="date"
            wire:model="form.match_date"
            id="match_date"
            name="match_date"
            class="mt-1 block w-full"/>
        @error('form.match_date')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input-label for="start_time" value="Start time" />
        <x-text-input
            type="time"
            wire:model="form.start_time"
            id="start_time"
            name="start_time"
            class="mt-1 block w-full"/>
        @error('form.start_time')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input-label for="venue_id" value="Venue" />
        <x-select-input wire:model="form.venue_id" id="venue_id" name="venue_id"
                        class="mt-1 block w-full"
                        :options="$venues" placeholder="Choose a venue" />
        @error('form.venue_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>
    </div>
</div>
