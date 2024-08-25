<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{
        open: false,
        theme: localStorage.theme,
        darkMode() {
            this.theme = 'dark'
            localStorage.theme = 'dark'
            ThemeSwitcher.setDarkClass()
        },
        lightMode() {
            this.theme = 'light'
            localStorage.theme = 'light'
            ThemeSwitcher.setDarkClass()
        },
        systemMode() {
            this.theme = undefined
            localStorage.removeItem('theme')
            ThemeSwitcher.setDarkClass()
        },
     }"
     class="shadow-lg dark:shadow-gray-700 md:shadow-none md:border-b-2 md:border-gray-100 md:dark:border-b md:dark:border-gray-700"
>
    <!-- Primary Navigation Menu -->
    <div class="px-4 md:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="h-14" />
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden md:flex md:items-center md:ms-6">
                <div class="hidden md:block">
                    <x-dropdown width="w-28 mt-4">
                        <x-slot name="trigger">
                            <x-heroicon-s-sun class="w-5 h-5 block text-gray-500 hover:text-gray-700 dark:hidden" />
                            <x-heroicon-s-moon
                                    class="w-5 h-5 hidden dark:block dark:text-gray-400 dark:hover:text-gray-300" />
                        </x-slot>
                        <x-slot name="content">
                            <button class="flex w-full items-center gap-3 px-3 py-2 text-start text-sm leading-5 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
                                    :class="theme === 'light' ? 'text-gray-900 font-medium' : 'text-gray-700 font-normal dark:text-gray-400 dark:font-normal'"
                                    @click="lightMode()"
                            >
                                <x-heroicon-o-sun class="w-5 h-5" ::class="theme === 'light' ? 'hidden' : 'block'" />
                                <x-heroicon-s-sun class="w-5 h-5" ::class="theme === 'light' ? 'block' : 'hidden'" />
                                {{ __('Light') }}
                            </button>
                            <button class="flex w-full items-center gap-3 px-3 py-2 text-start text-sm leading-5 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
                                    :class="theme === 'dark' ? 'dark:text-gray-100 dark:font-medium' : 'text-gray-700 font-normal dark:text-gray-400 dark:font-normal'"
                                    @click="darkMode()"
                            >
                                <x-heroicon-o-moon class="w-5 h-5" ::class="theme === 'dark' ? 'hidden' : 'block'" />
                                <x-heroicon-s-moon class="w-5 h-5" ::class="theme === 'dark' ? 'block' : 'hidden'" />
                                {{ __('Dark') }}
                            </button>
                            <button class="flex w-full items-center gap-3 px-3 py-2 text-start text-sm leading-5 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out"
                                    :class="theme === undefined ? 'text-gray-900 font-medium dark:text-gray-100 dark:font-medium' : 'text-gray-700 font-normal dark:text-gray-400 dark:font-normal'"
                                    @click="systemMode()"
                            >
                                <x-heroicon-o-computer-desktop class="w-5 h-5"
                                                               ::class="theme === undefined ? 'hidden' : 'block'" />
                                <x-heroicon-s-computer-desktop class="w-5 h-5"
                                                               ::class="theme === undefined  ? 'block' : 'hidden'" />
                                {{ __('System') }}
                            </button>
                        </x-slot>
                    </x-dropdown>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                                 x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center md:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden">
        <div class="pt-2 pb-3 space-y-1 columns-2">
            <x-navigation />
        </div>

        <div class="py-3 px-4 border-t border-gray-200 dark:border-gray-600 grid grid-cols-3 gap-3 md:hidden">
            <x-secondary-button @click="lightMode()" class="justify-center"
                                ::class="theme === 'light' ? 'border-gray-400' : ''">
                <x-heroicon-o-sun class="w-5 h-5 mr-2" ::class="theme === 'light' ? 'hidden' : 'block'" />
                <x-heroicon-s-sun class="w-5 h-5 mr-2" ::class="theme === 'light' ? 'block' : 'hidden'" />
                {{ __('Light') }}
            </x-secondary-button>
            <x-secondary-button @click="darkMode()" class="justify-center"
                                ::class="theme === 'dark' ? 'dark:border-white' : ''">
                <x-heroicon-o-moon class="w-5 h-5 mr-2" ::class="theme === 'dark' ? 'hidden' : 'block'" />
                <x-heroicon-s-moon class="w-5 h-5 mr-2" ::class="theme === 'dark' ? 'block' : 'hidden'" />
                {{ __('Dark') }}
            </x-secondary-button>
            <x-secondary-button @click="systemMode()" class="justify-center"
                                ::class="theme === undefined ? 'border-gray-400 dark:border-white' : ''">
                <x-heroicon-o-computer-desktop class="w-5 h-5 mr-2"
                                               ::class="theme === undefined ? 'hidden' : 'block'" />
                <x-heroicon-s-computer-desktop class="w-5 h-5 mr-2"
                                               ::class="theme === undefined ? 'block' : 'hidden'" />
                {{ __('System') }}
            </x-secondary-button>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200"
                     x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"
                     x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
