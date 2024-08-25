<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="overflow-hidden shadow-sm sm:rounded-lg p-6">
            {{ __("You're logged in!") }}
    </div>
</x-app-layout>
