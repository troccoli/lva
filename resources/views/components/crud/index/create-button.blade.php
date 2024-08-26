@props(['route' => '', 'label' => 'Add'])

@php
    $classes = "w-full inline-flex justify-center place-items-center gap-1 rounded-md bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-400 hover:dark:bg-indigo-600 px-3 py-2 text-center text-sm text-indigo-700 hover:text-indigo-50 dark:text-indigo-300 shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400";
@endphp

<a type="button" wire:navigate href="{{ route($route) }}" {{ $attributes->merge(['class' => $classes]) }}>
    <x-heroicon-s-plus class="h-4 w-5" />
    {{ $label }}
</a>
