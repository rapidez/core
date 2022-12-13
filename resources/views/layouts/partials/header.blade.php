<header class="mb-5 border-b shadow {{ Route::currentRouteName() == 'checkout' ? 'justify-between' : '' }}">
    <div class="container flex flex-wrap items-center">
        <div class="w-1/6 sm:w-3/12">
            <div class="text-xl sm:text-3xl ml-3">
                <a href="/" aria-label="@lang('Go to home')">
                    <span class="hidden sm:inline">
                        <img src="https://raw.githubusercontent.com/rapidez/art/master/logo.svg" height="48" width="152" alt="">
                    </span>
                    <span class="inline sm:hidden">🚀</span>
                </a>
            </div>
        </div>
        @if(Route::currentRouteName() !== 'checkout')
            <div class="w-6/12 h-12 flex items-center">
                @include('rapidez::layouts.partials.header.autocomplete')
            </div>
        @endif
        <div class="w-1/3 sm:w-1/4 flex justify-end pr-3">
            @include('rapidez::layouts.partials.header.account')
            @if(Route::currentRouteName() !== 'checkout')
                @include('rapidez::layouts.partials.header.minicart')
            @endif
        </div>
        @if(Route::currentRouteName() !== 'checkout')
            <nav class="w-full">
                {{-- Because the lack of an @includeIf or @includeWhen equivalent for Blade components we're using a placeholder --}}
                <x-dynamic-component :component="App::providerIsLoaded('Rapidez\Menu\MenuServiceProvider') ? 'menu' : 'placeholder'" />
            </nav>
        @endif
    </div>
</header>
