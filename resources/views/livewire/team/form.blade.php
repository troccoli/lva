<div class="space-y-6">
    <div>
        <x-input-label for="club_id" value="Club" />
        @empty($form->club_id)
            <x-select-input wire:model="form.club_id" id="club_id" name="club_id" class="mt-1 block w-full"
                            :options="$clubs" placeholder="Choose a club" />
        @else
            <x-text-input wire:model="form.clubName" id="club_id" name="club_id" type="text"
                          class="mt-1 block w-full" disabled />
        @endempty
        @error('form.club_id')
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
    <div>
        <x-input-label for="venue_id" value="Venue" />
        <x-select-input wire:model="form.venue_id" id="venue_id" name="venue_id" class="mt-1 block w-full"
                        :options="$venues" placeholder="Choose a venue" />
        @error('form.venue_id')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>
    </div>
</div>
