<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400">
    <livewire:layout.navigation />

    <div class="flex">
        <!-- Navigation -->
        <livewire:layout.sidebar />

        <div class="grow">
            <!-- Page Heading -->
            @if (isset($header))
                <header class="py-6 px-4 md:px-6 lg:px-8">
                    {{ $header }}
                </header>
            @endif

            <!-- Page Content -->
            <main>
                <div class="px-4 md:px-6 lg:px-8 text-gray-900 dark:text-gray-100">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</div>
</body>
</html>
