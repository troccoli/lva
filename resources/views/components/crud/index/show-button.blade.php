@props(['route' => '', 'model' => '', 'label' => ''])

@php
$label = empty($label) ? __('Show') : $label;
 @endphp

<a type="button" wire:navigate href="{{ route($route, $model) }}"
   class="inline-flex justify-center place-items-center gap-1 rounded-md bg-indigo-50 dark:bg-indigo-900/50 hover:bg-indigo-400 hover:dark:bg-indigo-600 px-3 py-2 text-center text-sm text-indigo-700 hover:text-indigo-50 dark:text-indigo-300 shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400"
>
    <x-heroicon-m-eye class="h-4 w-4" />
    <span class="hidden md:inline-block">{{ $label }}</span>
</a>
