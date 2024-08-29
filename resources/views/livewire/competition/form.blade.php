<div class="space-y-6">
    <div>
        <x-input-label for="season_id" value="Season" />
        @empty($form->season_id)
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
        <x-input-label for="name" value="Name" />
        <x-text-input wire:model="form.name" id="name" name="name" type="text" class="mt-1 block w-full"
                      autocomplete="name" placeholder="Name" />
        @error('form.name')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>
    </div>
</div>
