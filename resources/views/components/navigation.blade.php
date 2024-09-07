<x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
    Dashboard
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('seasons.index')" :active="request()->routeIs('seasons.*')" wire:navigate>
    Seasons
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('competitions.index')" :active="request()->routeIs('competitions.*')" wire:navigate>
    Competitions
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('divisions.index')" :active="request()->routeIs('divisions.*')" wire:navigate>
    Divisions
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('clubs.index')" :active="request()->routeIs('clubs.*')" wire:navigate>
    Clubs
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('teams.index')" :active="request()->routeIs('teams.*')" wire:navigate>
    Teams
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('venues.index')" :active="request()->routeIs('venues.*')" wire:navigate>
    Venues
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('people')" :active="request()->routeIs('people')" wire:navigate>
    People
</x-responsive-nav-link>
<x-responsive-nav-link :href="route('appointments')" :active="request()->routeIs('appointments')" wire:navigate>
    Appointments
</x-responsive-nav-link>
<hr class="hidden md:block"/>
<x-responsive-nav-link :href="route('fixtures.index')" :active="request()->routeIs('fixtures.*')" wire:navigate>
    Fixtures
</x-responsive-nav-link>
