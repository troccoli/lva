@props(['label', 'value'])

<div class="flex flex-row items-center">
    <x-input-label for="{{ $label.'_name' }}" value="{{ $label }}" class="w-1/5"/>
    <x-text-input
        value="{{ $value }}"
        name="{{ $label . '_name' }}"
        id="{{ $label . '_name' }}"
        type="text"
        class="ml-2 p-0 block w-full"
        disabled />
</div>
