<!doctype html>
<html lang="{{ strtok(Rapidez::config('general/locale/code'), '_') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="view-transition" content="same-origin" />

    <title>
        @hasSection('title')
            @config('design/head/title_prefix')
            @yield('title')
            @config('design/head/title_suffix')
        @else
            @config('design/head/default_title')
        @endif
    </title>

    <meta name="description" content="@yield('description', Rapidez::config('design/head/default_description'))"/>
    <meta name="robots" content="@yield('robots', Rapidez::config('design/search_engine_robots/default_robots'))"/>
    <link rel="canonical" href="@yield('canonical', url()->current())" />

    @php($configPath = route('config') . '?v=' . Cache::rememberForever('cachekey', fn () => md5(Str::random())) . '&s=' . config('rapidez.store'))
    <link href="{{ $configPath }}" rel="preload" as="script">
    <script defer src="{{ $configPath }}"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @config('design/head/includes')
</head>
<body class="text antialiased has-[.prevent-scroll:checked]:overflow-clip">
    <div id="app" class="flex flex-col min-h-dvh">
        @include('rapidez::layouts.partials.global-slideover')
        @includeWhen(!request()->routeIs('checkout'), 'rapidez::layouts.partials.header')
        @includeWhen(request()->routeIs('checkout'), 'rapidez::layouts.checkout.header')
        <main>
            @yield('content')
        </main>
        @includeWhen(!request()->routeIs('checkout'), 'rapidez::layouts.partials.footer')
        @includeWhen(request()->routeIs('checkout'), 'rapidez::layouts.checkout.footer')
        @stack('page_end')
    </div>

    @if (session('notifications'))
        <script async>
            document.addEventListener('vue:loaded', function() {
                @foreach (session('notifications') ?? [] as $notification)
                    window.Notify('{{ $notification['message'] }}', '{{ $notification['type'] ?? 'success' }}')
                @endforeach
            });
        </script>
    @endif
    @stack('foot')
    <script>window.config = { ...window.config ?? {}, ...@json(config('frontend')) }</script>
    <svg hidden class="hidden">
        @stack('bladeicons')
    </svg>
</body>
</html>
