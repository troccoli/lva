<x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
    {{ __('Dashboard') }}
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('seasons')" :active="request()->routeIs('seasons')" wire:navigate>
    {{ __('Seasons') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('competitions')" :active="request()->routeIs('competitions')" wire:navigate>
    {{ __('Competitions') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('divisions')" :active="request()->routeIs('divisions')" wire:navigate>
    {{ __('Divisions') }}
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('clubs')" :active="request()->routeIs('clubs')" wire:navigate>
    {{ __('Clubs') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teams')" :active="request()->routeIs('teams')" wire:navigate>
    {{ __('Teams') }}
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('venues')" :active="request()->routeIs('venues')" wire:navigate>
    {{ __('Venues') }}
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('people')" :active="request()->routeIs('people')" wire:navigate>
    {{ __('People') }}
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('appointments')" :active="request()->routeIs('appointments')" wire:navigate>
    {{ __('Appointments') }}
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('fixtures')" :active="request()->routeIs('fixtures')" wire:navigate>
    {{ __('Fixtures') }}
</x-responsive-nav-link>
