<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @stack('stylesheets')

    <!-- Breadcrumbs - for SEO -->
    {{ Breadcrumbs::view('breadcrumbs::json-ld') }}
</head>
<body>
    <div id="app">
        @include('layouts.partials.navbar')

        <main class="py-4">
            {{ Breadcrumbs::render() }}
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
