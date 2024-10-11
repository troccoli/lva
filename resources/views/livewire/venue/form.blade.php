<div class="mb-4 space-y-6">
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input
            wire:model="form.name"
            id="name"
            name="name"
            type="text"
            class="mt-1 block w-full"
            autocomplete="name"
            placeholder="Name with full address" />
        @error('form.name')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <x-primary-button>Save</x-primary-button>
</div>
