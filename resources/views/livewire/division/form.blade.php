<div class="space-y-6">
    <x-crud.chosen-filters-section>
        <x-crud.chosen-filter label="Season" value="{{ $this->form->seasonName }}"/>
        <x-crud.chosen-filter label="Competition" value="{{ $this->form->competitionName }}"/>
    </x-crud.chosen-filters-section>
    <div>
        <x-input-label for="name" value="Name" />
        <x-text-input
            wire:model="form.name"
            id="name"
            name="name"
            type="text"
            class="mt-1 block w-full"
            autocomplete="name"
            placeholder="Name" />
        @error('form.name')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>
    <div>
        <x-input-label for="display_order" value="Display Order" />
        <x-text-input
            wire:model="form.display_order"
            id="display_order"
            name="display_order"
            type="text"
            class="mt-1 block w-full"
            autocomplete="display_order"
            placeholder="Display Order" />
        @error('form.display_order')
        <x-input-error class="mt-2" :messages="$message" />
        @enderror
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Save</x-primary-button>
    </div>
</div>
