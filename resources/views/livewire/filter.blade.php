<div class="flex flex-col gap-2">
    <x-input-label for="{{ $label . '_filter' }}" value="{{ $label }}" />
    <x-select-input
        class="dark:bg-gray-700 dark:text-gray-100 dark:border-gray-100"
        wire:model="selectedOption"
        :name="$label . '_filter'"
        :id="$label . '_filter'"
        :$options
        :currentOption="$selectedOption"
        wire:change="$dispatchSelf('option-selected')"
    />
</div>
