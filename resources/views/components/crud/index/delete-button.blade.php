@props(['model' => '', 'label' => ''])

@php
    $label = empty($label) ? 'Delete' : $label;
@endphp

<div x-data="">
    <button
            class="inline-flex justify-center place-items-center gap-1 rounded-md bg-red-100 hover:bg-red-400 dark:bg-red-300 dark:hover:bg-red-200 px-3 py-2 text-center text-sm text-red-700 hover:text-red-50 dark:text-red-900 dark:hover:text-red-800 shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400"
            x-on:click.prevent="$dispatch('open-modal', 'confirm-deletion-{{ $model->getKey() }}')"
    >
        <x-heroicon-m-trash class="h-4 w-4" />
        <span class="hidden md:inline-block">{{ $label }}</span>
    </button>

    <x-modal name="confirm-deletion-{{ $model->getKey() }}" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="delete('{{ $model->getKey() }}')" class="p-6">
            <h2 class="text-lg text-wrap font-medium text-gray-900 dark:text-gray-100">
                {{ $slot }}
            </h2>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-danger-button class="ms-3">Delete</x-danger-button>
            </div>
        </form>
    </x-modal>
</div>
