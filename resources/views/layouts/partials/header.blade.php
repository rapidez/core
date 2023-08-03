<header class="relative mb-5 border-b shadow">
    <div class="container flex flex-wrap items-center max-sm:px-3">
        <input
            class="peer hidden"
            id="navigation"
            type="checkbox"
        />
        <div class="py-2">
            <div class="mr-3 flex items-center text-xl sm:text-3xl">
                <a
                    href="{{ url('/') }}"
                    aria-label="@lang('Go to home')"
                >
                    <x-icon-rapidez class="h-12 hidden sm:inline" />
                    <x-icon-r class="h-10 inline sm:hidden" />
                </a>
                <label
                    class="ml-3 cursor-pointer sm:hidden"
                    for="navigation"
                >
                    <x-heroicon-o-menu class="inline w-7" />
                </label>
            </div>
        </div>
        <div class="flex h-12 max-w-lg flex-1 items-center">
            @include('rapidez::layouts.partials.header.autocomplete')
        </div>
        <div class="ml-auto flex items-center justify-end pl-3">
            @include('rapidez::layouts.partials.header.account')
            @include('rapidez::layouts.partials.header.minicart')
        </div>
        <nav class="w-full">
            {{-- Because the lack of an @includeIf or @includeWhen equivalent for Blade components we're using a placeholder --}}
            <x-dynamic-component :component="App::providerIsLoaded('Rapidez\Menu\MenuServiceProvider') ? 'menu' : 'placeholder'" />
        </nav>
    </div>
</header>
