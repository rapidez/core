<title>
    @hasSection('title')
        {{ Rapidez::config('design/head/title_prefix') }}
        @yield('title')
        {{ Rapidez::config('design/head/title_suffix') }}
    @else
        {{ Rapidez::config('design/head/default_title') }}
    @endif
</title>
