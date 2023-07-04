<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', '')</title>
    <meta name="description" content="@yield('description', '')"/>
    <meta name="robots" content="@yield('robots', Rapidez::config('design/search_engine_robots/default_robots', 'INDEX,FOLLOW'))"/>
    <link rel="canonical" href="@yield('canonical', url()->current())" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="text-neutral antialiased">
    <div id="app" class="flex flex-col min-h-screen">
        @includeWhen(!request()->is('checkout'), 'rapidez::layouts.partials.header')
        @includeWhen(request()->is('checkout'), 'rapidez::layouts.checkout.header')
        <main>
            @yield('content')
        </main>
        @includeWhen(!request()->is('checkout'), 'rapidez::layouts.partials.footer')
        @includeWhen(request()->is('checkout'), 'rapidez::layouts.checkout.footer')
        @stack('page_end')
    </div>

    <script>window.config = @json(config('frontend'));</script>
    @stack('foot')
</body>
</html>
