<nav class="hidden md:block min-h-lvh min-w-40 border-r-2 border-gray-100 dark:border-gray-700">
    <!-- Navigation Links -->
    <div class="flex flex-col space-y-2.5 mt-4">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
            {{ __('Dashboard') }}
        </x-responsive-nav-link>
    </div>
</nav>
