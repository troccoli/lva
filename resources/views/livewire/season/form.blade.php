<div class="space-y-6">
    <div>
        <x-input-label for="year" value="Year" />
        <x-text-input
            wire:model="form.year"
            id="year" name="year"
            type="text"
            class="mt-1 block w-full"
            autocomplete="year"
            placeholder="Year" />
        @error('form.year')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>
    </div>
</div>
